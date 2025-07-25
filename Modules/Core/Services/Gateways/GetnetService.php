<?php

namespace Modules\Core\Services\Gateways;

use App\Jobs\ProcessWithdrawal;
use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Task;
use Modules\Core\Services\TaskService;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\PendingDebt;
use Modules\Core\Entities\PendingDebtWithdrawal;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\SaleService;
use Modules\Transfers\Services\GetNetStatementService;
use Modules\Withdrawals\Services\WithdrawalService;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Abstracts\GatewayServicesAbstract;

class GetnetService extends GatewayServicesAbstract
{
    public Company $company;
    public CompanyBankAccount $companyBankAccount;
    public $gatewayIds = [];

    public function __construct()
    {
        $this->gatewayIds = [Gateway::GETNET_PRODUCTION_ID, Gateway::GETNET_SANDBOX_ID];

        $this->gatewayName = 'Getnet';

        $this->companyColumnBalance = '';
    }

    public function getAvailableBalance(): int
    {
        $cacheName = "balance-available-getnet-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::whereIn("gateway_id", $this->gatewayIds)
                ->where("company_id", $this->company->id)
                ->where("is_waiting_withdrawal", 1)
                ->whereNull("withdrawal_id")
                ->sum("value");
        });
    }

    public function getPendingBalance(): int
    {
        $cacheName = "balance-pending-getnet-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("transactions.company_id", $this->company->id)
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereIn("transactions.gateway_id", $this->gatewayIds)
                ->where("transactions.is_waiting_withdrawal", 0)
                ->whereNull("transactions.withdrawal_id")
                ->sum("transactions.value");
        });
    }

    public function getPendingBalanceCount(): int
    {
        $cacheName = "balance-pending-count-getnet-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("transactions.company_id", $this->company->id)
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereIn("transactions.gateway_id", $this->gatewayIds)
                ->where("transactions.is_waiting_withdrawal", 0)
                ->whereNull("transactions.withdrawal_id")
                ->count();
        });
    }

    public function getBlockedBalance(): int
    {
        $cacheName = "balance-blocked-getnet-{$this->company->id}";
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
        $cacheName = "balance-blocked-count-getnet-{$this->company->id}";
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
        $cacheName = "balance-debt-getnet-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return PendingDebt::where("company_id", $this->company->id)
                ->doesntHave("withdrawals")
                ->whereNull("confirm_date")
                ->sum("value");
        });
    }

    public function createWithdrawal($value)
    {
        try {
            if ($this->company->asaas_balance < 0 && $value - $this->company->asaas_balance < 0) {
                throw new Exception("Saque negado devido ao saldo negativo no Asaas");
            }

            $isFirstUserWithdrawal = (new WithdrawalService())->isFirstUserWithdrawal($this->company->user_id);

            if ($isFirstUserWithdrawal) {
                TaskService::setCompletedTask($this->company->user, Task::find(Task::TASK_FIRST_WITHDRAWAL));
            }

            $withdrawal = Withdrawal::create([
                "value" => $value,
                "company_id" => $this->company->id,
                "transfer_type" => "TED",
                "bank" => $this->companyBankAccount->bank,
                "agency" => $this->companyBankAccount->agency,
                "agency_digit" => $this->companyBankAccount->agency_digit,
                "account" => $this->companyBankAccount->account,
                "account_digit" => $this->companyBankAccount->account_digit,
                "status" => Withdrawal::STATUS_PROCESSING,
                "tax" => 0,
                "observation" => $isFirstUserWithdrawal ? "Primeiro saque" : null,
                "automatic_liquidation" => true,
                "gateway_id" => foxutils()->isProduction() ? Gateway::GETNET_PRODUCTION_ID : Gateway::GETNET_SANDBOX_ID,
            ]);

            dispatch(new ProcessWithdrawal($withdrawal, $isFirstUserWithdrawal));

            return $withdrawal;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function processWithdrawal(Withdrawal $withdrawal, $isFirstUserWithdrawal): bool
    {
        try {
            DB::beginTransaction();

            $transactionsSum = $this->company
                ->transactions()
                ->whereIn("gateway_id", $this->gatewayIds)
                ->whereIn("status_enum", [
                    Transaction::STATUS_PAID,
                    Transaction::STATUS_TRANSFERRED,
                    Transaction::STATUS_CHARGEBACK,
                    Transaction::STATUS_REFUNDED,
                ])
                ->where("is_waiting_withdrawal", 1)
                ->whereNull("withdrawal_id")
                ->orderBy("id");

            $currentValue = 0;

            $transactionsSum->chunkById(2000, function ($transactions) use ($currentValue, $withdrawal) {
                foreach ($transactions as $transaction) {
                    $currentValue += $transaction->value;

                    if ($currentValue <= $withdrawal->value) {
                        $transaction->update([
                            "withdrawal_id" => $withdrawal->id,
                            "is_waiting_withdrawal" => false,
                        ]);
                    }
                }
            });

            $pendingDebts = PendingDebt::doesntHave("withdrawals")
                ->where("company_id", $this->company->id)
                ->whereNull("confirm_date")
                ->get(["id", "value"]);

            $pendingDebtsSum = 0;
            foreach ($pendingDebts as $pendingDebt) {
                $pendingDebtsSum += $pendingDebt->value;
                PendingDebtWithdrawal::create([
                    "pending_debt_id" => $pendingDebt->id,
                    "withdrawal_id" => $withdrawal->id,
                ]);
            }

            $withdrawal->update([
                "debt_pending_value" => $pendingDebtsSum,
                "status" => $withdrawal->present()->getStatus($isFirstUserWithdrawal ? "in_review" : "pending"),
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);

            return false;
        }
    }

    public function getLowerAndBiggerAvailableValues(int $withdrawalValueRequested): array
    {
        $availableBalance = $this->getAvailableBalance();

        $transactionsSum = $this->company
            ->transactions()
            ->whereIn("gateway_id", $this->gatewayIds)
            ->where("is_waiting_withdrawal", 1)
            ->whereNull("withdrawal_id")
            ->orderBy("id");

        $currentValue = 0;
        $lowerValue = 0;
        $biggerValue = 0;

        $transactionsSum->chunk(2000, function ($transactions) use (
            $withdrawalValueRequested,
            &$currentValue,
            &$lowerValue,
            &$biggerValue,
            &$availableBalance
        ) {
            foreach ($transactions as $transaction) {
                $currentValue += $transaction->value;

                if ($currentValue > $availableBalance) {
                    $biggerValue = $lowerValue;
                    $lowerValue = 0;
                    return;
                }

                if ($currentValue >= $withdrawalValueRequested) {
                    $lowerValue = $currentValue - $transaction->value;
                    $biggerValue = $currentValue;

                    return;
                }
                $lowerValue = $currentValue;
            }
        });

        return [
            "data" => [
                "lower_value" => $lowerValue,
                "bigger_value" => $biggerValue,
            ],
        ];
    }

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $availableBalance += $pendingBalance;

        $accountOwnerId = auth()->user()->account_owner_id ?? $sale->owner_id;
        $transaction = Transaction::where("sale_id", $sale->id)
            ->where("user_id", $accountOwnerId)
            ->first();

        return $availableBalance >= $transaction->value;
    }

    public function updateAvailableBalance($saleId = null)
    {
        try {
            $transactionModel = new Transaction();
            $getnetService = new GetnetBackOfficeService();

            $transactions = $transactionModel
                ->with("sale")
                ->where("release_date", "<=", Carbon::now()->format("Y-m-d"))
                ->where("status_enum", (new Transaction())->present()->getStatusEnum("paid"))
                ->where("is_waiting_withdrawal", 0)
                ->whereNull("withdrawal_id")
                ->whereNotNull("company_id")
                ->whereIn("gateway_id", $this->gatewayIds)
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

            $transactions->chunkById(100, function ($transactions) use ($getnetService) {
                foreach ($transactions as $transaction) {
                    try {
                        $sale = $transaction->sale;
                        $saleIdEncoded = Hashids::connection("sale_id")->encode($sale->id);

                        if (foxutils()->isProduction()) {
                            $subsellerId = $transaction->company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID);
                        } else {
                            $subsellerId = $transaction->company->getGatewaySubsellerId(Gateway::GETNET_SANDBOX_ID);
                        }

                        $getnetService->setStatementSubSellerId($subsellerId)->setStatementSaleHashId($saleIdEncoded);

                        $result = json_decode($getnetService->getStatement());

                        if (
                            !empty($result->list_transactions) &&
                            !is_null($result->list_transactions[0]) &&
                            !is_null($result->list_transactions[0]->details[0]) &&
                            !is_null($result->list_transactions[0]->details[0]->release_status) &&
                            $result->list_transactions[0]->details[0]->release_status == "N"
                        ) {
                            $transaction->update([
                                "is_waiting_withdrawal" => 1,
                            ]);
                        } elseif (empty($result->list_transactions)) {
                            throw new Exception(
                                "TransactionsService: A venda {$sale->id} não foi encontrada na getnet!"
                            );
                        }
                    } catch (Exception $e) {
                        report($e);
                    }
                }
            });
        } catch (Exception $e) {
            report($e);
        }
    }

    public function getStatement($filters)
    {
        if (!empty(request("sale"))) {
            request()->merge(["sale" => str_replace("#", "", request("sale"))]);
        }

        if (!empty($filters["sale"])) {
            $filters["sale"] = str_replace("#", "", $filters["sale"]);
        }

        $filtersAndStatement = (new GetNetStatementService())->getFiltersAndStatement($this->company->id);

        $filters = $filtersAndStatement["filters"];
        $result = json_decode($filtersAndStatement["statement"]);

        if (isset($result->errors)) {
            return response()->json($result->errors, 400);
        }

        $data = (new GetNetStatementService())->performWebStatement($result, $filters, 2000);

        return response()->json($data);
    }

    public function getResume()
    {
        $cacheName = "resume-getnet-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            $lastTransaction = Transaction::whereIn("gateway_id", $this->gatewayIds)
                ->where("company_id", $this->company->id)
                ->orderBy("id", "desc")
                ->first();

            if (empty($lastTransaction)) {
                return [];
            }
            $lastTransactionDate = $lastTransaction->created_at->format("d/m/Y");

            $pendingDebtBalance = $this->getPendingDebtBalance();
            $blockedBalance = $this->getBlockedBalance();
            $blockedBalanceCount = $this->getBlockedBalanceCount();
            $pendingBalance = $this->getPendingBalance();
            $pendingBalanceCount = $this->getPendingBalanceCount();
            $availableBalance = $this->getAvailableBalance();
            $totalBalance = $availableBalance + $pendingBalance;

            (new CompanyService())->applyBlockedBalance($this, $availableBalance, $pendingBalance, $blockedBalance);

            return [
                "name" => "Getnet",
                "available_balance" => $availableBalance,
                "pending_debt_balance" => $pendingDebtBalance,
                "pending_balance" => $pendingBalance,
                "pending_balance_count" => $pendingBalanceCount,
                "blocked_balance" => $blockedBalance,
                "blocked_balance_count" => $blockedBalanceCount,
                "total_balance" => $totalBalance,
                "total_available" => $availableBalance,
                "last_transaction" => $lastTransactionDate,
                "id" => "w7YL9jZD6gp4qmv",
            ];
        });
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::GETNET_PRODUCTION_ID : Gateway::GETNET_SANDBOX_ID;
    }

    public function cancel($sale, $response, $refundObservation): bool
    {
        try {
            DB::beginTransaction();
            $responseGateway = $response->response ?? [];
            $statusGateway = $response->status_gateway ?? "";

            SaleRefundHistory::create([
                "sale_id" => $sale->id,
                "refunded_amount" => foxutils()->onlyNumbers($sale->total_paid_value),
                "date_refunded" => Carbon::now(),
                "gateway_response" => json_encode($responseGateway),
                "refund_value" => foxutils()->onlyNumbers($sale->total_paid_value),
                "refund_observation" => $refundObservation,
                "user_id" => auth()->user()->account_owner_id ?? $sale->owner_id,
            ]);

            $refundTransactions = $sale->transactions;

            $saleService = new SaleService();

            foreach ($refundTransactions as $refundTransaction) {
                $transactionRefundAmount = $refundTransaction->value;

                $company = $refundTransaction->company;
                if (!empty($company)) {
                    $saleService->checkPendingDebt($sale, $company, $transactionRefundAmount);
                }

                $refundTransaction->status = "refunded";
                $refundTransaction->status_enum = Transaction::STATUS_REFUNDED;
                $refundTransaction->is_waiting_withdrawal = 0;
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
        return false;
    }
}
