<?php

namespace Modules\Core\Abstracts;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Entities\Gateway;
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
    const EXCLUDED_COMPANY_IDS = [802, 1112, 971, 1013, 1055, 945, 989, 1050, 236, 1116, 992, 622, 1026, 1068, 1029, 993, 1089, 23];
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
        $cacheName = "balance-security-reserve-{$this->gatewayName}-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return SecurityReserve::where("company_id", $this->company->id)
                ->where("status", SecurityReserve::STATUS_PENDING)
                ->sum("value");
        });
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

    public function getAvailableBalanceByTransfers(){
        $balance = DB::select("SELECT SUM(IF(type_enum=1,value,-value)) as total FROM transfers
        WHERE deleted_at is null and company_id = :companyId",["companyId"=>$this->company->id]);

        $pendingWithdrawal = Withdrawal::where("company_id", $this->company->id)
            ->whereNotIn("status", [Withdrawal::STATUS_AUTOMATIC_TRANSFERRED, Withdrawal::STATUS_TRANSFERRED, Withdrawal::STATUS_REFUSED, Withdrawal::STATUS_LIQUIDATING])
            ->sum("value");

        return intval($balance[0]->total ?? 0) - intval($pendingWithdrawal);
    }

    public function withdrawalValueIsValid($value): bool
    {
        if (empty($value) || $value < 1) {
            return false;
        }

        $availableBalance = $this->getAvailableBalanceByTransfers();
        $pendingBalance = $this->getPendingBalance();
        (new CompanyService())->applyBlockedBalance($this, $availableBalance, $pendingBalance);

        return $value <= $availableBalance;
    }

    public function existsBankAccountApproved()
    {
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

                if (!empty($this->companyBankAccount)) {
                    $data = array_merge($data, $this->setBankAccountArray($this->companyBankAccount));
                } else {
                    $data = array_merge($data, [
                        "transfer_type" => "PIX",
                        "type_key_pix" => $this->company->company_type == Company::JURIDICAL_PERSON ? "CNPJ" : "CPF",
                        "key_pix" => $this->company->document,
                    ]);
                }

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

            $transactions = Transaction::with(["company", "user", "sales"])
                ->where("release_date", "<=", Carbon::now()->format("Y-m-d"))
                ->where("status_enum", Transaction::STATUS_PAID)
                ->whereIn("gateway_id", $this->gatewayIds)
                ->whereNotNull("company_id");

            if (!empty($saleId)) {
                $transactions->where("sale_id", $saleId);
            }

            $columnBalanceName = $this->companyColumnBalance;

            $currentTime = Carbon::now()->format("H:i:s");

            // Check if the current date is a national holiday in Brazil
            try {
                $holidays = json_decode(file_get_contents('https://brasilapi.com.br/api/feriados/v1/' . Carbon::now()->year), true);
            } catch (Exception $e) {
                $holidays = [];
            }

            foreach ($transactions->cursor() as $transaction) {
                $company = $transaction->company;
                $user = $transaction->user;
                $sale = $transaction->sale;
                
                $isHoliday = false;
                foreach ($holidays as $holiday) {
                    if ($holiday['date'] == Carbon::now()->format('Y-m-d')) {
                        $isHoliday = true;
                        break;
                    }
                }

                 // trava de liberação de cartão por conta da IUGU segurar os saques
                //  if ($sale->payment_method == Sale::CREDIT_CARD_PAYMENT) {
                //     continue;
                // }

                if ($sale->payment_method == Sale::CREDIT_CARD_PAYMENT 
                        && ($isHoliday || ( !empty($company->credit_card_release_time) 
                                            && $currentTime < $company->credit_card_release_time)
                            )) {
                    continue;
                }
                $transaction->update([
                    "status" => "transfered",
                    "status_enum" => Transaction::STATUS_TRANSFERRED,
                ]);
                

                $hasSecurityReserve = false;
                $reserveValue = 0;

                if ($sale->payment_method == Sale::CREDIT_CARD_PAYMENT) {
                    $reserveValue = ceil(($transaction->value / 100) * (float) $user->security_reserve_tax);
                    $reserveDays = $user->security_reserve_days;
                    $security_reserve_tax = (float) $user->security_reserve_tax;
                    $hasSecurityReserve = true;
                } elseif ($sale->payment_method == Sale::PIX_PAYMENT) {
                    if ($user->security_reserve_tax_pix && (float) $user->security_reserve_tax_pix > 0) {
                        $reserveValue = ceil(($transaction->value / 100) * (float) $user->security_reserve_tax_pix);
                        $reserveDays = $user->security_reserve_days_pix;
                        $security_reserve_tax = (float) $user->security_reserve_tax_pix;
                        $hasSecurityReserve = true;
                    }
                } elseif ($sale->payment_method == Sale::BILLET_PAYMENT) {
                    if ($user->security_reserve_tax_billet && (float) $user->security_reserve_tax_billet > 0) {
                        $reserveValue = ceil(($transaction->value / 100) * (float) $user->security_reserve_tax_billet);
                        $reserveDays = $user->security_reserve_days_billet;
                        $security_reserve_tax = (float) $user->security_reserve_tax_billet;
                        $hasSecurityReserve = true;
                    }
                }

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

                if ($hasSecurityReserve) {
                    SecurityReserve::create([
                        "company_id" => $company->id,
                        "sale_id" => $transaction->sale_id,
                        "transaction_id" => $transaction->id,
                        "transfer_id" => $transfer->id,
                        "user_id" => $transaction->user_id,
                        "tax" => $security_reserve_tax,
                        "value" => $reserveValue,
                        "release_date" => Carbon::now()
                            ->addDays($reserveDays)
                            ->format("Y-m-d"),
                        "status" => SecurityReserve::STATUS_PENDING,
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
        }
    }

    public function updateAllCompaniesBalance()
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->setCompany($company);
            $availableBalanceByTransfers = $this->getAvailableBalanceByTransfers();
            $columnBalanceName = $this->companyColumnBalance;

            $company->update([
                $columnBalanceName => $availableBalanceByTransfers,
            ]);
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
                } else {
                    $securityReserve = SecurityReserve::where([
                        "sale_id" => $sale->id,
                        "status" => SecurityReserve::STATUS_PENDING,
                    ])->first();

                    if ($securityReserve) {
                        Transfer::create([
                            "transaction_id" => $securityReserve->transaction_id,
                            "user_id" => $securityReserve->user_id,
                            "company_id" => $refundTransaction->company->id,
                            "type_enum" => Transfer::TYPE_IN,
                            "value" => $securityReserve->value,
                            "type" => Transfer::TYPE_IN,
                            "reason" => "Liberação de reserva de segurança",
                            "gateway_id" => foxutils()->isProduction()
                                ? Gateway::SAFE2PAY_PRODUCTION_ID
                                : Gateway::SAFE2PAY_SANDBOX_ID,
                        ]);

                        $gatewayBalance += $securityReserve->value;

                        $refundTransaction->company->update([
                            $this->companyColumnBalance => $gatewayBalance,
                        ]);
                    }
                }

                $refundValue = $refundTransaction->value;
                if ($refundTransaction->type == Transaction::TYPE_PRODUCER) {
                    $refundValue = foxutils()->onlyNumbers($sale->total_paid_value);
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
