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
use Modules\Core\Services\FoxUtils;
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
            Gateway::CIELO_SANDBOX_ID ,
            // extrato cielo engloba as vendas antigas zoop e pagarme
            Gateway::PAGARME_PRODUCTION_ID,
            Gateway::PAGARME_SANDBOX_ID,
            Gateway::ZOOP_PRODUCTION_ID,
            Gateway::ZOOP_SANDBOX_ID
        ];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function getAvailableBalance() : int
    {
        if (!$this->company->user->show_old_finances){
            return 0;
        }

        return $this->company->cielo_balance;
    }

    public function getPendingBalance() : int
    {
        if (!$this->company->user->show_old_finances){
            return 0;
        }

        return Transaction::where('company_id', $this->company->id)
                            ->where('status_enum', Transaction::STATUS_PAID)
                            ->where(function($query) {
                                $query->whereIn('gateway_id', $this->gatewayIds)
                                    ->orWhere(function($query) {
                                        $query->where('gateway_id', Gateway::ASAAS_PRODUCTION_ID)->where('created_at', '<', '2021-09');
                                    });
                            })
                            ->where('is_waiting_withdrawal', 0)
                            ->whereNull('withdrawal_id')
                            ->sum('value');
    }

    public function getBlockedBalance() : int
    {
        if (!$this->company->user->show_old_finances){
            return 0;
        }

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
        if (!$this->company->user->show_old_finances){
            return 0;
        }

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

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $blockedBalance = $this->getBlockedBalance();
        $availableBalance += $pendingBalance;
        $availableBalance -= $blockedBalance;

        $transaction = Transaction::where('sale_id', $sale->id)->where('user_id', auth()->user()->account_owner_id)->first();

        return $availableBalance > $transaction->value;
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
        $availableBalance = $this->company->cielo_balance;
        $blockedBalance = $this->getBlockedBalance();
        $availableBalance -= $blockedBalance;

        if (empty($withdrawalValue) || $withdrawalValue < 1 || $withdrawalValue > $availableBalance) {
            return false;
        }

        return true;
    }

    public function createWithdrawal($withdrawalValue): bool
    {
        try {
            DB::beginTransaction();

            $this->company->update([
                'cielo_balance' => $this->company->cielo_balance -= $withdrawalValue
            ]);

            $withdrawal = Withdrawal::where([
                                        ['company_id', $this->company->id],
                                        ['status', Withdrawal::STATUS_PENDING],
                                ])
                                ->whereIn('gateway_id', $this->gatewayIds)
                                ->first();

            if (empty($withdrawal)) {

                $isFirstUserWithdrawal = (new WithdrawalService)->isFirstUserWithdrawal($this->company->user_id);

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
                        'gateway_id' => foxutils()->isProduction() ? Gateway::CIELO_PRODUCTION_ID : Gateway::CIELO_SANDBOX_ID
                    ]
                );
            } else {
                $withdrawalValueSum = $withdrawal->value + $withdrawalValue;

                $withdrawal->update([
                    'value' => $withdrawalValueSum,
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
                        'gateway_id' => foxutils()->isProduction() ? Gateway::CIELO_PRODUCTION_ID : Gateway::CIELO_SANDBOX_ID
                    ]
                );

                $company->update([
                    'cielo_balance' => $company->cielo_balance + $transaction->value
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

    public function getResume()
    {
        if(!$this->company->user->show_old_finances) {
            return [];
        }

        $lastTransaction = Transaction::whereIn('gateway_id', $this->gatewayIds)
                                        ->where('company_id', $this->company->id)
                                        ->orderBy('id', 'desc')->first();

        if(empty($lastTransaction)) {
            return [];
        }

        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $blockedBalance = $this->getBlockedBalance();
        $totalBalance = $availableBalance + $pendingBalance - $blockedBalance;
        $availableBalance -= $blockedBalance;
        $lastTransactionDate = $lastTransaction->created_at->format('d/m/Y');

        return [
            'name' => 'Cielo',
            'available_balance' => foxutils()->formatMoney($availableBalance / 100),
            'pending_balance' => foxutils()->formatMoney($pendingBalance / 100),
            'blocked_balance' => foxutils()->formatMoney($blockedBalance / 100),
            'total_balance' => foxutils()->formatMoney($totalBalance / 100),
            'last_transaction' => $lastTransactionDate,
            'id' => 'pM521rZJrZeaXoQ'
        ];
    }

    public function getGatewayAvailable()
    {
        if(!$this->company->user->show_old_finances) {
            return [];
        }

        $lastTransaction = DB::table('transactions')->whereIn('gateway_id', $this->gatewayIds)
                                        ->where('company_id', $this->company->id)
                                        ->orderBy('id', 'desc')->first();

        return !empty($lastTransaction) ? ['Cielo']:[];
    }

    public function getGatewayId()
    {
        return FoxUtils::isProduction() ? Gateway::CIELO_PRODUCTION_ID:Gateway::CIELO_SANDBOX_ID;
    }
}
