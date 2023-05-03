<?php

namespace Modules\Core\Abstracts;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Entities\SecurityReserve;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\StatementService;
use Modules\Core\Services\TaskService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

abstract class GatewayServicesAbstract
{
    public Company $company;
    public CompanyBankAccount $companyBankAccount;
    public $gatewayIds = [];
    public $gatewayName = "Vega";
    public $companyColumnBalance = "vega_balance";
    public $companyId;
    public $gatewayHashId = "pqbz5KZby37dLlm";

    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function setBankAccount(CompanyBankAccount $companyBankAccount)
    {
        $this->companyBankAccount = $companyBankAccount;
    }

    public function getAvailableBalance(): int
    {
        $columnName = $this->companyColumnBalance;
        return $this->company->$columnName;
    }

    public function getPendingBalance(): int
    {
        $cacheName = "balance-pending-{$this->gatewayName}-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("transactions.company_id", $this->company->id)
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereIn("transactions.gateway_id", $this->gatewayIds)
                ->sum("transactions.value");
        });
    }

    public function getSecurityReserveBalance(): int
    {
        // $cacheName = "balance-security-reserve-{$this->gatewayName}-{$this->company->id}";
        // return cache()->remember($cacheName, 120, function () {
        return SecurityReserve::where("company_id", $this->company->id)
            ->where("status", SecurityReserve::STATUS_PENDING)
            ->sum("value");
        // });
    }

    public function getPendingBalanceCount(): int
    {
        $cacheName = "balance-pending-count-{$this->gatewayName}-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("transactions.company_id", $this->company->id)
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereIn("transactions.gateway_id", $this->gatewayIds)
                ->count();
        });
    }

    public function getBlockedBalance(): int
    {
        $cacheName = "balance-blocked-{$this->gatewayName}-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("company_id", $this->company->id)
                ->whereIn("gateway_id", $this->gatewayIds)
                ->whereIn("status_enum", [Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID])
                ->join("block_reason_sales", "block_reason_sales.sale_id", "=", "transactions.sale_id")
                ->where("block_reason_sales.status", BlockReasonSale::STATUS_BLOCKED)
                ->sum("value");
        });
    }

    public function getBlockedBalanceCount(): int
    {
        $cacheName = "balance-blocked-count-{$this->gatewayName}-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("company_id", $this->company->id)
                ->whereIn("gateway_id", $this->gatewayIds)
                ->join("block_reason_sales", "block_reason_sales.sale_id", "=", "transactions.sale_id")
                ->where("block_reason_sales.status", BlockReasonSale::STATUS_BLOCKED)
                ->count();
        });
    }

    public function getPendingDebtBalance(): int
    {
        return 0;
    }

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $availableBalance += $pendingBalance;

        if ($sale->payment_method == Sale::BILLET_PAYMENT) {
            return $availableBalance >= (int) foxutils()->onlyNumbers($sale->total_paid_value);
        }
        $accountOwnerId = auth()->user()->account_owner_id ?? $sale->owner_id;
        $transaction = Transaction::where("sale_id", $sale->id)
            ->where("user_id", $accountOwnerId)
            ->first();
        return $availableBalance >= $transaction->value;
    }

    public function getWithdrawals(): JsonResource
    {
        $withdrawals = Withdrawal::where("company_id", $this->company->id)
            ->whereIn("gateway_id", $this->gatewayIds)
            ->orderBy("id", "DESC");

        return WithdrawalResource::collection($withdrawals->paginate(10));
    }

    public function withdrawalValueIsValid($value): bool
    {
        if (empty($value) || $value < 1) {
            return false;
        }

        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        (new CompanyService())->applyBlockedBalance($this, $availableBalance, $pendingBalance);

        return $value <= $availableBalance;
    }

    public function existsBankAccountApproved()
    {
        //verifica se existe uma conta bancaria aprovada
        $companyBankAccount = $this->company->getDefaultBankAccount() ?? null;
        if (empty($companyBankAccount)) {
            return false;
        }
        $this->companyBankAccount = $companyBankAccount;
        return true;
    }

    public function createWithdrawal($value)
    {
        try {
            DB::beginTransaction();

            $columnBalanceName = $this->companyColumnBalance;
            $this->company->update([
                $this->companyColumnBalance => $this->company->$columnBalanceName - $value,
            ]);

            $withdrawal = Withdrawal::where([
                ["company_id", $this->company->id],
                ["status", Withdrawal::STATUS_PENDING],
            ])
                ->whereIn("gateway_id", $this->gatewayIds)
                ->first();

            if (empty($withdrawal)) {
                $isFirstUserWithdrawal = (new WithdrawalService())->isFirstUserWithdrawal($this->company->user_id);

                if ($isFirstUserWithdrawal) {
                    TaskService::setCompletedTask($this->company->user, Task::find(Task::TASK_FIRST_WITHDRAWAL));
                }

                $data = [
                    "value" => $value,
                    "company_id" => $this->company->id,
                    "status" => $isFirstUserWithdrawal ? Withdrawal::STATUS_IN_REVIEW : Withdrawal::STATUS_PENDING,
                    "tax" => 0,
                    "observation" => $isFirstUserWithdrawal ? "Primeiro saque" : null,
                    "gateway_id" => $this->getGatewayId(),
                ];

                $data = array_merge($data, $this->setBankAccountArray($this->companyBankAccount));

                $withdrawal = Withdrawal::create($data);
            } else {
                $withdrawalValueSum = $withdrawal->value + $value;
                $data = [
                    "value" => $withdrawalValueSum,
                ];

                if ($withdrawal->transfer_type != $this->companyBankAccount->transfer_type) {
                    $data = array_merge($data, $this->setBankAccountArray($this->companyBankAccount));
                }

                $withdrawal->update($data);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return false;
        }

        return $withdrawal;
    }

    public function setBankAccountArray($bankAccount)
    {
        switch ($bankAccount->transfer_type) {
            case "TED":
                return [
                    "transfer_type" => "TED",
                    "bank" => $bankAccount->bank,
                    "agency" => $bankAccount->agency,
                    "agency_digit" => $bankAccount->agency_digit,
                    "account" => $bankAccount->account,
                    "account_digit" => $bankAccount->account_digit,
                ];
                break;
            case "PIX":
                return [
                    "transfer_type" => "PIX",
                    "type_key_pix" => $bankAccount->type_key_pix,
                    "key_pix" => $bankAccount->key_pix,
                ];
                break;
        }
        return [];
    }

    public function updateAvailableBalance($saleId = null)
    {
        try {
            DB::beginTransaction();

            $transactions = Transaction::with(["company", "user"])
                ->where("release_date", "<=", Carbon::now()->format("Y-m-d"))
                ->where("status_enum", Transaction::STATUS_PAID)
                ->whereIn("gateway_id", $this->gatewayIds)
                ->whereNotNull("company_id");
            // ->where(function ($where) {
            //     $where->where("tracking_required", false)->orWhereHas("sale", function ($query) {
            //         $query->where(function ($q) {
            //             $q->where("has_valid_tracking", true)->orWhereNull("delivery_id");
            //         });
            //     });
            // });

            if (!empty($saleId)) {
                $transactions->where("sale_id", $saleId);
            }

            $columnBalanceName = $this->companyColumnBalance;

            foreach ($transactions->cursor() as $transaction) {
                $transaction->update([
                    "status" => "transfered",
                    "status_enum" => Transaction::STATUS_TRANSFERRED,
                ]);

                $company = $transaction->company;
                $user = $transaction->user;

                $reserveValue = ceil(($transaction->value / 100) * $user->security_reserve_tax);
                $transferValue = $transaction->value - $reserveValue;

                $company->update([
                    $this->companyColumnBalance => $company->$columnBalanceName + $transferValue,
                ]);

                $transfer = Transfer::create([
                    "transaction_id" => $transaction->id,
                    "user_id" => $company->user_id,
                    "company_id" => $company->id,
                    "type_enum" => Transfer::TYPE_IN,
                    "value" => $transferValue,
                    "type" => "in",
                    "gateway_id" => $this->getGatewayId(),
                ]);

                SecurityReserve::create([
                    "company_id" => $company->id,
                    "sale_id" => $transaction->sale_id,
                    "transaction_id" => $transaction->id,
                    "transfer_id" => $transfer->id,
                    "user_id" => $transaction->user_id,
                    "tax" => $user->security_reserve_tax,
                    "value" => $reserveValue,
                    "release_date" => Carbon::now()
                        ->addDays($user->security_reserve_days)
                        ->format("Y-m-d"),
                    "status" => SecurityReserve::STATUS_PENDING,
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
        }
    }

    public function getStatement($filters)
    {
        return (new StatementService())->getDefaultStatement($this->company->id, $this->gatewayIds, $filters);
    }

    public function getPeriodBalance($filters)
    {
        return (new StatementService())->getPeriodBalance($this->company->id, $this->gatewayIds, $filters);
    }

    public function getResume()
    {
        $cacheName = "resume-{$this->gatewayName}-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            $lastTransaction = Transaction::whereIn("gateway_id", $this->gatewayIds)
                ->where("company_id", $this->company->id)
                ->orderBy("id", "desc")
                ->first();
            $lastTransactionDate = !empty($lastTransaction) ? $lastTransaction->created_at->format("d/m/Y") : "";

            $blockedBalance = $this->getBlockedBalance();
            $blockedBalanceCount = $this->getBlockedBalanceCount();
            $pendingBalance = $this->getPendingBalance();
            $securityReserveBalance = $this->getSecurityReserveBalance();
            $pendingBalanceCount = $this->getPendingBalanceCount();
            $availableBalance = $this->getAvailableBalance();
            $totalBalance = $availableBalance + $pendingBalance + $securityReserveBalance;

            (new CompanyService())->applyBlockedBalance($this, $availableBalance, $pendingBalance, $blockedBalance);

            return [
                "name" => $this->gatewayName,
                "available_balance" => $availableBalance,
                "pending_balance" => $pendingBalance,
                "security_reserve_balance" => $securityReserveBalance,
                "pending_balance_count" => $pendingBalanceCount,
                "blocked_balance" => $blockedBalance,
                "blocked_balance_count" => $blockedBalanceCount,
                "total_balance" => $totalBalance,
                "total_available" => $availableBalance,
                "last_transaction" => $lastTransactionDate,
                "pending_debt_balance" => 0,
                "id" => $this->gatewayHashId,
            ];
        });
    }

    public function getGatewayAvailable()
    {
        $lastTransaction = DB::table("transactions")
            ->whereIn("gateway_id", $this->gatewayIds)
            ->where("company_id", $this->company->id)
            ->orderBy("id", "desc")
            ->first();

        return !empty($lastTransaction) ? [$this->gatewayName] : [];
    }

    public function getCompanyApiKey(Sale $sale)
    {
        $company = $sale
            ->transactions()
            ->where("type", Transaction::TYPE_PRODUCER)
            ->first()->company;

        $this->companyId = $company->id;
        $this->apiKey = $company->getGatewayApiKey($this->getGatewayId());
    }

    abstract public function getGatewayId(): int;

    public function cancel($sale, $response, $refundObservation): bool
    {
        try {
            DB::beginTransaction();
            $responseGateway = $response->response ?? [];
            $statusGateway = $response->status_gateway ?? "";
            $saleIdEncode = hashids_encode($sale->id, "sale_id");

            SaleRefundHistory::create([
                "sale_id" => $sale->id,
                "refunded_amount" => foxutils()->onlyNumbers($sale->total_paid_value),
                "date_refunded" => Carbon::now(),
                "gateway_response" => json_encode($responseGateway),
                "refund_value" => foxutils()->onlyNumbers($sale->total_paid_value),
                "refund_observation" => $refundObservation,
                "user_id" => auth()->user()->account_owner_id ?? $sale->owner_id,
            ]);

            $saleService = new SaleService();
            $saleTax = 0;

            $cashbackValue = $sale->cashback()->first()->value ?? 0;
            $saleTax = $saleService->getSaleTaxRefund($sale, $cashbackValue);

            $totalSale = $saleService->getSaleTotalValue($sale);
            $gatewayBalance = 0;
            foreach ($sale->transactions as $refundTransaction) {
                if (empty($refundTransaction->company_id)) {
                    $refundTransaction->update([
                        "status_enum" => Transaction::STATUS_REFUNDED,
                        "status" => "refunded",
                    ]);
                    continue;
                }

                $gatewayBalance = $refundTransaction->company->vega_balance;

                if ($refundTransaction->status_enum == Transaction::STATUS_PAID) {
                    Transfer::create([
                        "transaction_id" => $refundTransaction->id,
                        "user_id" => $refundTransaction->company->user_id,
                        "company_id" => $refundTransaction->company->id,
                        "type_enum" => Transfer::TYPE_IN,
                        "value" => $refundTransaction->value,
                        "type" => "in",
                        "gateway_id" => $this->getGatewayId(),
                    ]);
                    $gatewayBalance += $refundTransaction->value;
                    $refundTransaction->company->update([
                        $this->companyColumnBalance => $gatewayBalance,
                    ]);
                }

                $refundValue = $refundTransaction->value;
                if ($refundTransaction->type == Transaction::TYPE_PRODUCER) {
                    $refundValue += $saleTax;
                }

                if ($refundValue > $totalSale) {
                    $refundValue = $totalSale;
                }

                Transfer::create([
                    "transaction_id" => $refundTransaction->id,
                    "user_id" => $refundTransaction->user_id,
                    "company_id" => $refundTransaction->company_id,
                    "gateway_id" => $sale->gateway_id,
                    "value" => $refundValue,
                    "type" => "out",
                    "type_enum" => Transfer::TYPE_OUT,
                    "reason" => "Estorno #{$saleIdEncode}",
                    "is_refunded_tax" => 0,
                ]);

                $refundTransaction->company->update([
                    $this->companyColumnBalance => $gatewayBalance - $refundValue,
                ]);

                $refundTransaction->status = "refunded";
                $refundTransaction->status_enum = Transaction::STATUS_REFUNDED;
                $refundTransaction->save();
            }

            $sale->update([
                "status" => Sale::STATUS_REFUNDED,
                "gateway_status" => $statusGateway,
                "refund_value" => foxutils()->onlyNumbers($sale->total_paid_value),
                "date_refunded" => Carbon::now(),
            ]);

            SaleService::createSaleLog($sale->id, "refunded");

            DB::commit();

            return true;
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            throw $ex;
        }
    }

    public function refundEnabled(): bool
    {
        return true;
    }

    public function canRefund(Sale $sale): bool
    {
        if ($sale->status != Sale::STATUS_APPROVED) {
            return false;
        }

        switch ($sale->payment_method) {
            case Sale::CREDIT_CARD_PAYMENT:
                return true;

            case Sale::BILLET_PAYMENT:
                return false;

            case Sale::PIX_PAYMENT:
                return Carbon::now()->diffInDays($sale->end_date) < 90;
        }

        return false;
    }
}
