<?php

namespace Modules\Core\Services\Gateways;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\WithdrawalRequestEvent;
use Modules\Core\Interfaces\Statement;
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

        return $this->company->balance;
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
        return false;
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
        $availableBalance = $this->company->balance;
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
                'balance' => $this->company->balance -= $withdrawalValue
            ]);

            $withdrawal = Withdrawal::where([
                                        ['company_id', $this->company->id],
                                        ['status', Withdrawal::STATUS_PENDING],
                                ])
                                ->whereIn('gateway_id', $this->gatewayIds)
                                ->first();

            if (empty($withdrawal)) {

                $isFirstUserWithdrawal = (new WithdrawalService)->isFirstUserWithdrawal(auth()->user);

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

        event(new WithdrawalRequestEvent($withdrawal));

        return true;
    }

    public function getStatement()
    {

    }
}
