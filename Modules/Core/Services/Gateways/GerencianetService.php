<?php

namespace Modules\Core\Services\Gateways;

use App\Jobs\ProcessWithdrawal;
use Exception;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Interfaces\Statement;
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
            // Gateway::GERENCIANET_SANDBOX_ID 
        ];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function getAvailableBalance() : int
    {
        return Transaction::whereIn('gateway_id', $this->gatewayIds)
                            ->where('company_id', $this->company->id)
                            ->where('is_waiting_withdrawal', 1)
                            ->whereNull('withdrawal_id')
                            ->sum('value');
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
                            ->where('status_enum', Transaction::STATUS_PENDING)
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

    public function createWithdrawal($withdrawalValue): bool
    {
        $isFirstUserWithdrawal = (new WithdrawalService)->isFirstUserWithdrawal(auth()->user());

        try {
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
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            report($e);

            return false;
        }
    }

    public function getLowerAndBiggerAvailableValues(Company $company, int $withdrawalValueRequested): array
    {
        $transactionsSum = $company->transactions()
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
        return false;
    }

    public function getStatement()
    {

    }


}
