<?php

namespace Modules\Core\Services\Gateways;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\StatementService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class CieloService implements Statement
{
    public Company $company;
    public $gatewayIds = [];

    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::CIELO_PRODUCTION_ID,
            Gateway::CIELO_SANDBOX_ID,
            // extrato cielo engloba as vendas antigas zoop e pagarme
            Gateway::PAGARME_PRODUCTION_ID,
            Gateway::PAGARME_SANDBOX_ID,
            Gateway::ZOOP_PRODUCTION_ID,
            Gateway::ZOOP_SANDBOX_ID,
        ];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function getAvailableBalance(): int
    {
        if (!$this->company->user->show_old_finances) {
            return 0;
        }
        return $this->company->cielo_balance;
    }

    public function getPendingBalance(): int
    {
        if (!$this->company->user->show_old_finances) {
            return 0;
        }

        return Transaction::leftJoin("block_reason_sales as brs", function ($join) {
            $join->on("brs.sale_id", "=", "transactions.sale_id")->where("brs.status", BlockReasonSale::STATUS_BLOCKED);
        })
            ->whereNull("brs.id")
            ->where("transactions.company_id", $this->company->id)
            ->where("transactions.status_enum", Transaction::STATUS_PAID)
            ->where(function ($query) {
                $query->whereIn("transactions.gateway_id", $this->gatewayIds)->orWhere(function ($query) {
                    $query
                        ->where("transactions.gateway_id", Gateway::ASAAS_PRODUCTION_ID)
                        ->where("transactions.created_at", "<", "2021-09");
                });
            })
            ->sum("transactions.value");
    }

    public function getPendingBalanceCount(): int
    {
        if (!$this->company->user->show_old_finances) {
            return 0;
        }

        return Transaction::leftJoin("block_reason_sales as brs", function ($join) {
            $join->on("brs.sale_id", "=", "transactions.sale_id")->where("brs.status", BlockReasonSale::STATUS_BLOCKED);
        })
            ->whereNull("brs.id")
            ->where("transactions.company_id", $this->company->id)
            ->where("transactions.status_enum", Transaction::STATUS_PAID)
            ->where(function ($query) {
                $query->whereIn("transactions.gateway_id", $this->gatewayIds)->orWhere(function ($query) {
                    $query
                        ->where("transactions.gateway_id", Gateway::ASAAS_PRODUCTION_ID)
                        ->where("transactions.created_at", "<", "2021-09");
                });
            })
            ->count();
    }

    public function getBlockedBalance(): int
    {
        if (!$this->company->user->show_old_finances) {
            return 0;
        }

        return Transaction::where("company_id", $this->company->id)
            ->whereIn("gateway_id", $this->gatewayIds)
            ->whereIn("status_enum", [Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID])
            ->join("block_reason_sales", "block_reason_sales.sale_id", "=", "transactions.sale_id")
            ->where("block_reason_sales.status", BlockReasonSale::STATUS_BLOCKED)
            ->sum("value");
    }

    public function getBlockedBalanceCount(): int
    {
        if (!$this->company->user->show_old_finances) {
            return 0;
        }

        return Transaction::where("company_id", $this->company->id)
            ->whereIn("gateway_id", $this->gatewayIds)
            ->join("block_reason_sales", "block_reason_sales.sale_id", "=", "transactions.sale_id")
            ->where("block_reason_sales.status", BlockReasonSale::STATUS_BLOCKED)
            ->count();
    }

    public function getPendingDebtBalance(): int
    {
        return 0;
    }

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $blockedBalance = $this->getBlockedBalance();
        $availableBalance += $pendingBalance;

        $accountOwnerId = auth()->user()->account_owner_id ?? $sale->owner_id;
        $transaction = Transaction::where("sale_id", $sale->id)
            ->where("user_id", $accountOwnerId)
            ->first();

        return $availableBalance > $transaction->value;
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

        if ($value > $availableBalance) {
            return false;
        }

        return true;
    }

    public function createWithdrawal($withdrawalValue)
    {
        try {
            DB::beginTransaction();

            $this->company->update([
                "cielo_balance" => ($this->company->cielo_balance -= $withdrawalValue),
            ]);

            $withdrawal = Withdrawal::where([
                ["company_id", $this->company->id],
                ["status", Withdrawal::STATUS_PENDING],
            ])
                ->whereIn("gateway_id", $this->gatewayIds)
                ->first();

            if (empty($withdrawal)) {
                $isFirstUserWithdrawal = (new WithdrawalService())->isFirstUserWithdrawal($this->company->user_id);

                $withdrawal = Withdrawal::create([
                    "value" => $withdrawalValue,
                    "company_id" => $this->company->id,
                    "bank" => $this->company->bank,
                    "agency" => $this->company->agency,
                    "agency_digit" => $this->company->agency_digit,
                    "account" => $this->company->account,
                    "account_digit" => $this->company->account_digit,
                    "status" => $isFirstUserWithdrawal ? Withdrawal::STATUS_IN_REVIEW : Withdrawal::STATUS_PENDING,
                    "tax" => 0,
                    "observation" => $isFirstUserWithdrawal ? "Primeiro saque" : null,
                    "gateway_id" => foxutils()->isProduction()
                        ? Gateway::CIELO_PRODUCTION_ID
                        : Gateway::CIELO_SANDBOX_ID,
                ]);
            } else {
                $withdrawalValueSum = $withdrawal->value + $withdrawalValue;

                $withdrawal->update([
                    "value" => $withdrawalValueSum,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return false;
        }

        return $withdrawal;
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
                        ? Gateway::CIELO_PRODUCTION_ID
                        : Gateway::CIELO_SANDBOX_ID,
                ]);

                $company->update([
                    "cielo_balance" => $company->cielo_balance + $transaction->value,
                ]);

                $transaction->update([
                    "status" => "transfered",
                    "status_enum" => Transaction::STATUS_TRANSFERRED,
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

    public function getResume()
    {
        if (!$this->company->user->show_old_finances) {
            return [];
        }

        $lastTransaction = Transaction::whereIn("gateway_id", $this->gatewayIds)
            ->where("company_id", $this->company->id)
            ->orderBy("id", "desc")
            ->first();

        if (empty($lastTransaction)) {
            return [];
        }
        $lastTransactionDate = $lastTransaction->created_at->format("d/m/Y");

        $blockedBalance = $this->getBlockedBalance();;
        $blockedBalanceCount = $this->getBlockedBalanceCount();;
        $pendingBalance = $this->getPendingBalance();
        $pendingBalanceCount = $this->getPendingBalanceCount();
        $availableBalance = $this->getAvailableBalance();
        $totalBalance = $availableBalance + $pendingBalance;

        (new CompanyService())->applyBlockedBalance($this, $availableBalance, $pendingBalance, $blockedBalance);

        return [
            "name" => "Cielo",
            "available_balance" => $availableBalance,
            "pending_balance" => $pendingBalance,
            "pending_balance_count" => $pendingBalanceCount,
            "blocked_balance" => $blockedBalance,
            "blocked_balance_count" => $blockedBalanceCount,
            "total_balance" => $totalBalance,
            "total_available" => $availableBalance,
            "pending_debt_balance" => 0,
            "last_transaction" => $lastTransactionDate,
            "id" => "pM521rZJrZeaXoQ",
        ];
    }

    public function getGatewayAvailable()
    {
        if (!$this->company->user->show_old_finances) {
            return [];
        }

        $lastTransaction = DB::table("transactions")
            ->whereIn("gateway_id", $this->gatewayIds)
            ->where("company_id", $this->company->id)
            ->orderBy("id", "desc")
            ->first();

        return !empty($lastTransaction) ? ["Cielo"] : [];
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::CIELO_PRODUCTION_ID : Gateway::CIELO_SANDBOX_ID;
    }

    public function refundEnabled(): bool
    {
        return false;
    }

    public function canRefund(Sale $sale): bool
    {
        return false;
    }
}
