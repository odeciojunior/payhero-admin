<?php

namespace Modules\Core\Services\Gateways;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Task;
use Modules\Core\Services\TaskService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\StatementService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class Safe2PayService implements Statement
{
    public Company $company;
    public $gatewayIds = [];
    public $apiKey;
    public $companyId;

    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::SAFE2PAY_PRODUCTION_ID,
            Gateway::SAFE2PAY_SANDBOX_ID
        ];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function getAvailableBalanceWithoutBlocking(): int
    {
        return $this->company->safe2pay_balance;
    }

    public function getAvailableBalance(): int
    {
        return $this->getAvailableBalanceWithoutBlocking() - $this->getBlockedBalance();
    }

    public function getPendingBalance(): int
    {
        return Transaction::leftJoin('block_reason_sales as brs', function ($join) {
            $join->on('brs.sale_id', '=', 'transactions.sale_id')
                ->where('brs.status', BlockReasonSale::STATUS_BLOCKED);
        })->whereNull('brs.id')
            ->where('transactions.company_id', $this->company->id)
            ->where('transactions.status_enum', Transaction::STATUS_PAID)
            ->whereIn('transactions.gateway_id', $this->gatewayIds)
            ->sum('transactions.value');
    }

    public function getBlockedBalance(): int
    {
        return Transaction::where('company_id', $this->company->id)
            ->whereIn('gateway_id', $this->gatewayIds)
            ->where('status_enum', Transaction::STATUS_TRANSFERRED)
            ->join('block_reason_sales', 'block_reason_sales.sale_id', '=', 'transactions.sale_id')
            ->where('block_reason_sales.status', BlockReasonSale::STATUS_BLOCKED)
            ->sum('value');
    }

    public function getBlockedBalancePending(): int
    {
        return Transaction::where('company_id', $this->company->id)
            ->whereIn('gateway_id', $this->gatewayIds)
            ->where('status_enum', Transaction::STATUS_PAID)
            ->join('block_reason_sales', 'block_reason_sales.sale_id', '=', 'transactions.sale_id')
            ->where('block_reason_sales.status', BlockReasonSale::STATUS_BLOCKED)
            ->sum('value');
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

        if ($sale->payment_method == Sale::BOLETO_PAYMENT) {
            return $availableBalance >= (int)foxutils()->onlyNumbers($sale->total_paid_value);
        } else {
            $transaction = Transaction::where('sale_id', $sale->id)->where('user_id', auth()->user()->account_owner_id)->first();
            return $availableBalance >= $transaction->value;
        }

    }

    public function getWithdrawals(): JsonResource
    {
        $withdrawals = Withdrawal::where('company_id', $this->company->id)
            ->whereIn('gateway_id', $this->gatewayIds)
            ->orderBy('id', 'DESC');

        return WithdrawalResource::collection($withdrawals->paginate(10));
    }

    public function withdrawalValueIsValid($value): bool
    {
        $availableBalance = $this->getAvailableBalance();

        if (empty($value) || $value < 1 || $value > $availableBalance) {
            return false;
        }

        return true;
    }

    public function createWithdrawal($value)
    {
        try {
            DB::beginTransaction();

            $this->company->update([
                'safe2pay_balance' => $this->company->safe2pay_balance - $value
            ]);

            $withdrawal = Withdrawal::where([
                ['company_id', $this->company->id],
                ['status', Withdrawal::STATUS_PENDING],
            ])
                ->whereIn('gateway_id', $this->gatewayIds)
                ->first();

            if (empty($withdrawal)) {

                $isFirstUserWithdrawal = (new WithdrawalService)->isFirstUserWithdrawal($this->company->user_id);

                if ($isFirstUserWithdrawal) {
                    TaskService::setCompletedTask(
                        $this->company->user,
                        Task::find(Task::TASK_FIRST_WITHDRAWAL)
                    );
                }

                $withdrawal = Withdrawal::create(
                    [
                        'value' => $value,
                        'company_id' => $this->company->id,
                        'bank' => $this->company->bank,
                        'agency' => $this->company->agency,
                        'agency_digit' => $this->company->agency_digit,
                        'account' => $this->company->account,
                        'account_digit' => $this->company->account_digit,
                        'status' => $isFirstUserWithdrawal ? Withdrawal::STATUS_IN_REVIEW : Withdrawal::STATUS_PENDING,
                        'tax' => 0,
                        'observation' => $isFirstUserWithdrawal ? 'Primeiro saque' : null,
                        'gateway_id' => foxutils()->isProduction() ? Gateway::SAFE2PAY_PRODUCTION_ID : Gateway::SAFE2PAY_SANDBOX_ID
                    ]
                );
            } else {
                $withdrawalValueSum = $withdrawal->value + $value;

                $withdrawal->update([
                    'value' => $withdrawalValueSum
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

            $transactions = Transaction::with('company')
                ->where('release_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where('status_enum', Transaction::STATUS_PAID)
                ->whereIn('gateway_id', $this->gatewayIds)
                ->whereNotNull('company_id')
                ->where(function ($where) {
                    $where->where('tracking_required', false)
                        ->orWhereHas('sale', function ($query) {
                            $query->where(function ($q) {
                                $q->where('has_valid_tracking', true)
                                    ->orWhereNull('delivery_id');
                            });
                        });
                });

            if (!empty($saleId)) {
                $transactions->where('sale_id', $saleId);
            }

            foreach ($transactions->cursor() as $transaction) {
                $company = $transaction->company;

                Transfer::create(
                    [
                        'transaction_id' => $transaction->id,
                        'user_id' => $company->user_id,
                        'company_id' => $company->id,
                        'type_enum' => Transfer::TYPE_IN,
                        'value' => $transaction->value,
                        'type' => 'in',
                        'gateway_id' => foxutils()->isProduction() ? Gateway::SAFE2PAY_PRODUCTION_ID : Gateway::SAFE2PAY_SANDBOX_ID
                    ]
                );

                $company->update([
                    'safe2pay_balance' => $company->safe2pay_balance + $transaction->value
                ]);

                $transaction->update([
                    'status' => 'transfered',
                    'status_enum' => Transaction::STATUS_TRANSFERRED,
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
        return (new StatementService)->getDefaultStatement($this->company->id, $this->gatewayIds, $filters);
    }

    public function getPeriodBalance($filters)
    {
        return (new StatementService)->getPeriodBalance($this->company->id, $this->gatewayIds, $filters);
    }

    public function getResume()
    {
        $lastTransaction = Transaction::whereIn('gateway_id', $this->gatewayIds)
            ->where('company_id', $this->company->id)
            ->orderBy('id', 'desc')->first();

        $pendingBalance = $this->getPendingBalance();
        $blockedBalance = $this->getBlockedBalance();
        $availableBalance = $this->getAvailableBalanceWithoutBlocking() - $blockedBalance;
        $blockedBalancePending = $this->getBlockedBalancePending();

        $totalBlockedBalance = $blockedBalance + $blockedBalancePending;
        $totalBalance = $availableBalance + $pendingBalance + $totalBlockedBalance;
        $lastTransactionDate = !empty($lastTransaction) ? $lastTransaction->created_at->format('d/m/Y') : '';

        return [
            'name' => 'Vega',
            'available_balance' => foxutils()->formatMoney($availableBalance / 100),
            'pending_balance' => foxutils()->formatMoney($pendingBalance / 100),
            'blocked_balance' => foxutils()->formatMoney($totalBlockedBalance / 100),
            'total_balance' => foxutils()->formatMoney($totalBalance / 100),
            'total_available' => $availableBalance,
            'last_transaction' => $lastTransactionDate,
            'id' => 'BeYEwR3AdgdKykA'
        ];
    }

    public function getGatewayAvailable()
    {
        $lastTransaction = DB::table('transactions')->whereIn('gateway_id', $this->gatewayIds)
            ->where('company_id', $this->company->id)
            ->orderBy('id', 'desc')->first();

        return !empty($lastTransaction) ? ['Vega'] : [];
    }

    public function getCompanyApiKey(Sale $sale)
    {
        $company = $sale->transactions()->where('type', Transaction::TYPE_PRODUCER)->first()->company;

        $this->companyId = $company->id;
        $this->apiKey = $company->getGatewayApiKey(foxutils()->isProduction() ? Gateway::SAFE2PAY_PRODUCTION_ID : Gateway::SAFE2PAY_SANDBOX_ID);

    }

    public function getGatewayId()
    {
        return FoxUtils::isProduction() ? Gateway::SAFE2PAY_PRODUCTION_ID : Gateway::SAFE2PAY_SANDBOX_ID;
    }

    public function cancel($sale, $response, $refundObservation): bool
    {
        try {
            DB::beginTransaction();
            $responseGateway = $response->response ?? [];
            $statusGateway = $response->status_gateway ?? '';

            SaleRefundHistory::create(
                [
                    'sale_id' => $sale->id,
                    'refunded_amount' => foxutils()->onlyNumbers($sale->total_paid_value),
                    'date_refunded' => Carbon::now(),
                    'gateway_response' => json_encode($responseGateway),
                    'refund_value' => foxutils()->onlyNumbers($sale->total_paid_value),
                    'refund_observation' => $refundObservation,
                    'user_id' => auth()->user()->account_owner_id,
                ]
            );

            $saleService = new SaleService();
            $saleTax = 0;

            $cashbackValue = $sale->cashback()->first()->value ?? 0;
            $saleTax = $saleService->getSaleTaxRefund($sale, $cashbackValue);

            $totalSale = $saleService->getSaleTotalValue($sale);
            $safe2payBalance = 0;
            foreach ($sale->transactions as $refundTransaction) {

                if (empty($refundTransaction->company_id)) {
                    $refundTransaction->update([
                        'status_enum' => Transaction::STATUS_REFUNDED,
                        'status' => 'refunded',
                    ]);
                    continue;
                }

                $safe2payBalance = $refundTransaction->company->safe2pay_balance;

                if ($refundTransaction->status_enum == Transaction::STATUS_PAID) {

                    Transfer::create(
                        [
                            'transaction_id' => $refundTransaction->id,
                            'user_id' => $refundTransaction->company->user_id,
                            'company_id' => $refundTransaction->company->id,
                            'type_enum' => Transfer::TYPE_IN,
                            'value' => $refundTransaction->value,
                            'type' => 'in',
                            'gateway_id' => foxutils()->isProduction() ? Gateway::SAFE2PAY_PRODUCTION_ID : Gateway::SAFE2PAY_SANDBOX_ID
                        ]
                    );
                    $safe2payBalance+= $refundTransaction->value;
                    $refundTransaction->company->update([
                        'safe2pay_balance' => $safe2payBalance
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
                    'transaction_id' => $refundTransaction->id,
                    'user_id' => $refundTransaction->user_id,
                    'company_id' => $refundTransaction->company_id,
                    'gateway_id' => $sale->gateway_id,
                    'value' => $refundValue,
                    'type' => 'out',
                    'type_enum' => Transfer::TYPE_OUT,
                    'reason' => 'refunded',
                    'is_refunded_tax' => 0
                ]);

                $refundTransaction->company->update([
                    'safe2pay_balance' => $safe2payBalance - $refundValue
                ]);

                $refundTransaction->status = 'refunded';
                $refundTransaction->status_enum = Transaction::STATUS_REFUNDED;
                $refundTransaction->save();
            }

            $sale->update([
                'status' => Sale::STATUS_REFUNDED,
                'gateway_status' => $statusGateway,
                'refund_value' => foxutils()->onlyNumbers($sale->total_paid_value),
                'date_refunded' => Carbon::now(),
            ]);

            SaleLog::create(
                [
                    'sale_id' => $sale->id,
                    'status' => 'refunded',
                    'status_enum' => Sale::STATUS_REFUNDED,
                ]
            );

            DB::commit();

            return true;
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            throw $ex;
        }
    }

}
