<?php

namespace Modules\Core\Services\Gateways;

use App\Jobs\ProcessWithdrawal;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\StatementService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class GerencianetService implements Statement
{

    public Company $company;
    public $gatewayIds = [];

    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::GERENCIANET_PRODUCTION_ID,
            Gateway::GERENCIANET_SANDBOX_ID
        ];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function getAvailableBalanceWithoutBlocking() : int
    {
        return Transaction::whereIn('gateway_id', $this->gatewayIds)
                            ->where('company_id', $this->company->id)
                            ->where('is_waiting_withdrawal', 1)
                            ->whereNull('withdrawal_id')
                            ->sum('value');
    }

    public function getAvailableBalance() : int
    {        
        return $this->getAvailableBalanceWithoutBlocking() - $this->getBlockedBalance();
    }

    public function getPendingBalance() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->where('status_enum', Transaction::STATUS_PAID)
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('is_waiting_withdrawal', 0)
                            ->whereNull('withdrawal_id')
                            ->sum('value');
    }

    public function getBlockedBalance() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('status_enum', Transaction::STATUS_TRANSFERRED)
                            ->whereHas('blockReasonSale',function ($query) {
                                    $query->where('status', BlockReasonSale::STATUS_BLOCKED);
                            })
                            ->sum('value');
    }

    public function getBlockedBalancePending() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('status_enum', Transaction::STATUS_PAID)
                            ->whereHas('blockReasonSale',function ($query) {
                                    $query->where('status', BlockReasonSale::STATUS_BLOCKED);
                            })
                            ->sum('value');
    }

    public function getPendingDebtBalance() : int
    {
        return 0;
    }

    public function getWithdrawals(): JsonResource
    {
        $withdrawals = Withdrawal::where('company_id', $this->company->id)
                                    ->whereIn('gateway_id', $this->gatewayIds)
                                    ->orderBy('id', 'DESC');

        return WithdrawalResource::collection($withdrawals->paginate(10));
    }

    public function withdrawalValueIsValid($withdrawalValue): bool
    {
        $availableBalance = $this->getAvailableBalance();

        if (empty($withdrawalValue) || $withdrawalValue < 1 || $withdrawalValue > $availableBalance) {
            return false;
        }

        return true;
    }

    public function createWithdrawal($withdrawalValue)
    {
        try {

            $isFirstUserWithdrawal = (new WithdrawalService)->isFirstUserWithdrawal($this->company->user_id);

            if ((new WithdrawalService)->isNotFirstWithdrawalToday($this->company->id, foxutils()->isProduction() ? Gateway::GERENCIANET_PRODUCTION_ID : Gateway::GERENCIANET_SANDBOX_ID)) {
                return false;
            }

            DB::beginTransaction();
            $withdrawal = Withdrawal::create(
                [
                    'value' => $withdrawalValue,
                    'company_id' => $this->company->id,
                    'bank' => $this->company->bank,
                    'agency' => $this->company->agency,
                    'agency_digit' => $this->company->agency_digit,
                    'account' => $this->company->account,
                    'account_digit' => $this->company->account_digit,
                    'status' => $isFirstUserWithdrawal ? Withdrawal::STATUS_IN_REVIEW : Withdrawal::STATUS_PENDING,
                    'tax' => 0,
                    'observation' => $isFirstUserWithdrawal ? 'Primeiro saque' : null,
                    'automatic_liquidation' => true,
                    'gateway_id' => foxutils()->isProduction() ? Gateway::GERENCIANET_PRODUCTION_ID : Gateway::GERENCIANET_SANDBOX_ID
                ]
            );

            $transactionsSum = $this->company->transactions()
                                        ->where('is_waiting_withdrawal', 1)
                                        ->whereIn('gateway_id', $this->gatewayIds)
                                        ->whereNull('withdrawal_id')
                                        ->orderBy('id');

            $currentValue = 0;

            $transactionsSum->chunkById(
                2000,
                function ($transactions) use ($currentValue, $withdrawal) {
                    foreach ($transactions as $transaction) {
                        $currentValue += $transaction->value;

                        if ($currentValue <= $withdrawal->value) {
                            $transaction->update(
                                [
                                    'withdrawal_id' => $withdrawal->id,
                                    'is_waiting_withdrawal' => false
                                ]
                            );
                        }
                    }
                }
            );

            DB::commit();
            return $withdrawal;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);

            return false;
        }
    }

    public function getLowerAndBiggerAvailableValues($withdrawalValueRequested): array
    {
        $transactionsSum = $this->company->transactions()
            ->whereIn('gateway_id', $this->gatewayIds)
            ->where('is_waiting_withdrawal', 1)
            ->whereNull('withdrawal_id')
            ->orderBy('id');

        $currentValue = 0;
        $lowerValue = 0;
        $biggerValue = 0;

        $transactionsSum->chunk(
            2000,
            function ($transactions) use ($withdrawalValueRequested, $currentValue, &$lowerValue, &$biggerValue) {
                foreach ($transactions as $transaction) {
                    $currentValue += $transaction->value;
                    if ($currentValue >= $withdrawalValueRequested) {
                        $lowerValue = $currentValue - $transaction->value;
                        $biggerValue = $currentValue;

                        return;
                    }
                }
            }
        );

        return [
            'data' => [
                'lower_value' => $lowerValue,
                'bigger_value' => $biggerValue,
            ]
        ];
    }

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $availableBalance += $pendingBalance;
        
        $transaction = Transaction::where('sale_id', $sale->id)->where('user_id', auth()->user()->account_owner_id)->first();

        return $availableBalance >= $transaction->value;
    }

    public function updateAvailableBalance($saleId = null)
    {
        try {
            $transactions = Transaction::with('company')
                ->where('release_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where('status_enum', Transaction::STATUS_PAID)
                ->where('is_waiting_withdrawal', 0)
                ->whereNull('withdrawal_id')
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

            $transactions->chunkById(100, function ($transactions) {
                foreach ($transactions as $transaction) {

                    Transfer::create(
                        [
                            'transaction_id' => $transaction->id,
                            'user_id' => $transaction->company->user_id,
                            'company_id' => $transaction->company->id,
                            'type_enum' => Transfer::TYPE_IN,
                            'value' => $transaction->value,
                            'type' => 'in',
                            'gateway_id' => foxutils()->isProduction() ? Gateway::GERENCIANET_PRODUCTION_ID : Gateway::GERENCIANET_SANDBOX_ID
                        ]
                    );

                    $transaction->update([
                            'is_waiting_withdrawal' => 1,
                        ]);
                }
            });
        } catch (Exception $e) {
            report($e);
        }
    }

    public function getStatement($filters)
    {
        return (new StatementService)->getDefaultStatement($this->company->id, $this->gatewayIds, $filters);
    }

    public function getResume()
    {
        $lastTransaction = Transaction::whereIn('gateway_id', $this->gatewayIds)
                                        ->where('company_id', $this->company->id)
                                        ->orderBy('id', 'desc')->first();

        if(empty($lastTransaction)) {
            return [];
        }
        
        $pendingBalance = $this->getPendingBalance();
        $blockedBalance = $this->getBlockedBalance();
        $availableBalance = $this->getAvailableBalanceWithoutBlocking() - $blockedBalance;
        $blockedBalancePending = $this->getBlockedBalancePending();
        
        $totalBlockedBalance = $blockedBalance + $blockedBalancePending;
        $totalBalance = $availableBalance + $pendingBalance + $totalBlockedBalance;
        $lastTransactionDate = $lastTransaction->created_at->format('d/m/Y');

        return [
            'name' => 'Gerencianet',
            'available_balance' => foxutils()->formatMoney($availableBalance / 100),
            'pending_balance' => foxutils()->formatMoney($pendingBalance / 100),
            'blocked_balance' => foxutils()->formatMoney($totalBlockedBalance / 100),
            'total_balance' => foxutils()->formatMoney($totalBalance / 100),
            'total_available' => $availableBalance,
            'last_transaction' => $lastTransactionDate,
            'id' => 'oXlqv13043xbj4y'
        ];
    }

    public function getGatewayAvailable(){
        $lastTransaction = DB::table('transactions')->whereIn('gateway_id', $this->gatewayIds)
                                        ->where('company_id', $this->company->id)
                                        ->orderBy('id', 'desc')->first();

        return !empty($lastTransaction) ? ['Gerencianet']:[];
    }

    public function getGatewayId()
    {
        return FoxUtils::isProduction() ? Gateway::GERENCIANET_PRODUCTION_ID:Gateway::GERENCIANET_SANDBOX_ID;
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

            $refundTransactions = $sale->transactions;

            foreach ($refundTransactions as $refundTransaction) {

                $refundTransaction->status = 'refunded';
                $refundTransaction->status_enum = Transaction::STATUS_REFUNDED;
                $refundTransaction->is_waiting_withdrawal = 0;
                $refundTransaction->save();
            }

            $sale->update(
                [
                    'status' => Sale::STATUS_REFUNDED,
                    'gateway_status' => $statusGateway,
                    'refund_value' => foxutils()->onlyNumbers($sale->total_paid_value),
                    'date_refunded' => Carbon::now(),
                ]
            );

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
