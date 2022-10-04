<?php

namespace Modules\Core\Services\Gateways;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Task;
use Modules\Core\Services\TaskService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\BonusBalance;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\StatementService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class IuguService implements Statement
{
    public Company $company;
    public $companyBankAccount;
    public $gatewayIds = [];
    public $apiKey;
    public $companyId;

    public function __construct()
    {
        $this->gatewayIds = [Gateway::IUGU_PRODUCTION_ID, Gateway::IUGU_SANDBOX_ID];
    }

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
        return $this->company->iugu_balance;
    }

    public function getPendingBalance(): int
    {
        $cacheName = "balance-pending-iugu-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("transactions.company_id", $this->company->id)
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereIn("transactions.gateway_id", $this->gatewayIds)
                ->sum("transactions.value");
        });
    }

    public function getPendingBalanceCount(): int
    {
        $cacheName = "balance-pending-count-iugu-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("transactions.company_id", $this->company->id)
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereIn("transactions.gateway_id", $this->gatewayIds)
                ->count();
        });
    }

    public function getBlockedBalance(): int
    {
        $cacheName = "balance-blocked-iugu-{$this->company->id}";
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
        $cacheName = "balance-blocked-count-iugu-{$this->company->id}";
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
        $this->companyBankAccount = $this->company->getDefaultBankAccount();
        return !empty($this->companyBankAccount);
    }

    public function createWithdrawal($value)
    {
        try {
            DB::beginTransaction();

            $this->company->update([
                "iugu_balance" => $this->company->iugu_balance - $value,
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
                    "gateway_id" => foxutils()->isProduction()
                        ? Gateway::IUGU_PRODUCTION_ID
                        : Gateway::IUGU_SANDBOX_ID,
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

            $transactions = Transaction::with("company")
                ->where("release_date", "<=", Carbon::now()->format("Y-m-d"))
                ->where("status_enum", Transaction::STATUS_PAID)
                ->whereIn("gateway_id", $this->gatewayIds)
                ->whereNotNull("company_id")
                ->where(function ($where) {
                    $where->where("tracking_required", false)->orWhereHas("sale", function ($query) {
                        $query->where(function ($q) {
                            $q->where("has_valid_tracking", true)->orWhereNull("delivery_id");
                        });
                    });
                });

            if (!empty($saleId)) {
                $transactions->where("sale_id", $saleId);
            }

            foreach ($transactions->cursor() as $transaction) {
                $company = $transaction->company;

                Transfer::create([
                    "transaction_id" => $transaction->id,
                    "user_id" => $company->user_id,
                    "company_id" => $company->id,
                    "type_enum" => Transfer::TYPE_IN,
                    "value" => $transaction->value,
                    "type" => "in",
                    "gateway_id" => foxutils()->isProduction()
                        ? Gateway::IUGU_PRODUCTION_ID
                        : Gateway::IUGU_SANDBOX_ID,
                ]);

                $company->update([
                    "iugu_balance" => $company->iugu_balance + $transaction->value,
                ]);

                $transaction->update([
                    "status" => "transfered",
                    "status_enum" => Transaction::STATUS_TRANSFERRED,
                ]);

                if ($transaction->type != Transaction::TYPE_PRODUCER) {
                    continue;
                }

                $bonusBalance = BonusBalance::where("user_id", $company->user_id)
                    ->where("expires_at", ">=", today())
                    ->where("current_value", ">", 0)
                    ->first();

                if (empty($bonusBalance)) {
                    continue;
                }

                $cloudfoxTransaction = Transaction::where("sale_id", $transaction->sale_id)
                    ->whereNull("company_id")
                    ->first();

                $taxValue = $cloudfoxTransaction->value - $transaction->sale->interest_total_value;

                if ($bonusBalance->current_value >= $taxValue) {
                    $bonusBalance->update([
                        "current_value" => $bonusBalance->current_value - $taxValue,
                    ]);
                } else {
                    $taxValue = $bonusBalance->current_value;

                    $bonusBalance->update([
                        "current_value" => 0,
                    ]);
                }

                Transfer::create([
                    "transaction_id" => $transaction->id,
                    "user_id" => $company->user_id,
                    "company_id" => $company->id,
                    "type_enum" => Transfer::TYPE_IN,
                    "value" => $taxValue,
                    "type" => "in",
                    "reason" => "Saldo bônus da transação ",
                    "gateway_id" => foxutils()->isProduction()
                        ? Gateway::IUGU_PRODUCTION_ID
                        : Gateway::IUGU_SANDBOX_ID,
                ]);

                $company->update([
                    "iugu_balance" => $company->iugu_balance + $taxValue,
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
        $cacheName = "resume-iugu-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            $lastTransaction = Transaction::whereIn("gateway_id", $this->gatewayIds)
                ->where("company_id", $this->company->id)
                ->orderBy("id", "desc")
                ->first();
            $lastTransactionDate = !empty($lastTransaction) ? $lastTransaction->created_at->format("d/m/Y") : "";

            $blockedBalance = $this->getBlockedBalance();
            $blockedBalanceCount = $this->getBlockedBalanceCount();
            $pendingBalance = $this->getPendingBalance();
            $pendingBalanceCount = $this->getPendingBalanceCount();
            $availableBalance = $this->getAvailableBalance();
            $totalBalance = $availableBalance + $pendingBalance;

            (new CompanyService())->applyBlockedBalance($this, $availableBalance, $pendingBalance, $blockedBalance);

            return [
                "name" => "Vega",
                "available_balance" => $availableBalance,
                "pending_balance" => $pendingBalance,
                "pending_balance_count" => $pendingBalanceCount,
                "blocked_balance" => $blockedBalance,
                "blocked_balance_count" => $blockedBalanceCount,
                "total_balance" => $totalBalance,
                "total_available" => $availableBalance,
                "last_transaction" => $lastTransactionDate,
                "pending_debt_balance" => 0,
                "id" => "QnVroegNzgKwjdb",
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

        return !empty($lastTransaction) ? ["Vega"] : [];
    }

    public function getCompanyApiKey(Sale $sale)
    {
        $company = $sale
            ->transactions()
            ->where("type", Transaction::TYPE_PRODUCER)
            ->first()->company;

        $this->companyId = $company->id;
        $this->apiKey = $company->getGatewayApiKey(
            foxutils()->isProduction() ? Gateway::IUGU_PRODUCTION_ID : Gateway::IUGU_SANDBOX_ID
        );
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::IUGU_PRODUCTION_ID : Gateway::IUGU_SANDBOX_ID;
    }

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
            $safe2payBalance = 0;
            foreach ($sale->transactions as $refundTransaction) {
                if (empty($refundTransaction->company_id)) {
                    $refundTransaction->update([
                        "status_enum" => Transaction::STATUS_REFUNDED,
                        "status" => "refunded",
                    ]);
                    continue;
                }

                $safe2payBalance = $refundTransaction->company->iugu_balance;

                if ($refundTransaction->status_enum == Transaction::STATUS_PAID) {
                    Transfer::create([
                        "transaction_id" => $refundTransaction->id,
                        "user_id" => $refundTransaction->company->user_id,
                        "company_id" => $refundTransaction->company->id,
                        "type_enum" => Transfer::TYPE_IN,
                        "value" => $refundTransaction->value,
                        "type" => "in",
                        "gateway_id" => foxutils()->isProduction()
                            ? Gateway::IUGU_PRODUCTION_ID
                            : Gateway::IUGU_SANDBOX_ID,
                    ]);
                    $safe2payBalance += $refundTransaction->value;
                    $refundTransaction->company->update([
                        "iugu_balance" => $safe2payBalance,
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
                    "iugu_balance" => $safe2payBalance - $refundValue,
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
