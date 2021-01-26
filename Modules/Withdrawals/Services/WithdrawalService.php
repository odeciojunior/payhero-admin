<?php

namespace Modules\Withdrawals\Services;

use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;

class WithdrawalService
{

    public function requestWithdrawal($company, $withdrawalValue): Withdrawal
    {
        $withdrawalModel = new Withdrawal();
        $isFirstUserWithdrawal = $this->isFirstUserWithdrawal($company->user);

        $withdrawal = $withdrawalModel->create(
            [
                'value'                 => $withdrawalValue,
                'company_id'            => $company->id,
                'bank'                  => $company->bank,
                'agency'                => $company->agency,
                'agency_digit'          => $company->agency_digit,
                'account'               => $company->account,
                'account_digit'         => $company->account_digit,
                'status'                => $withdrawalModel->present()->getStatus($isFirstUserWithdrawal ? 'in_review' : 'pending'),
                'tax'                   => 0,
                'observation'           => $isFirstUserWithdrawal ? 'Primeiro saque' : null,
                'automatic_liquidation' => true,
            ]
        );

        $transactionSum = $this->getTransactionsWaitingWithdrawalAmount($withdrawalValue, $withdrawal);
        $withdrawal->update(['value' => Transaction::where('withdrawal_id', $withdrawal->id)->sum('value')]);

        return $withdrawal;
    }

    public function isFirstUserWithdrawal($user): bool
    {
        $withdrawalModel = new Withdrawal();
        $withdrawalStatus = [
            $withdrawalModel->present()->getStatus('liquidating'),
            $withdrawalModel->present()->getStatus('partially_liquidated'),
            $withdrawalModel->present()->getStatus('transfered')
        ];

        $isFirstUserWithdrawal = false;
        $userWithdrawal = $withdrawalModel->whereHas(
            'company',
            function ($query) use ($user) {
                $query->where('user_id', $user->account_owner_id);
            }
        )
            ->whereIn('status', $withdrawalStatus)
            ->exists();

        if (!$userWithdrawal) {
            $isFirstUserWithdrawal = true;
        }

        return $isFirstUserWithdrawal;
    }

    public function setWaitingTransactionsWaitingToWithdrawal($withdrawalValue, $withdrawal)
    {
        $transactionsSum = $withdrawal->company->transactions()
            ->whereIn('gateway_id', [14, 15])
            ->where('is_waiting_withdrawal', 1)
            ->whereNull('withdrawal_id')
            ->orderBy('id');

        $currentValue = 0;

        $test = function ($transactions) use (
            $currentValue,
            $withdrawalValue,
            $withdrawal
        ) {
            foreach ($transactions as $transaction) {
                $currentValue += $transaction->value;

                if ($currentValue <= $withdrawalValue) {
                    $transaction->update(
                        [
                            'withdrawal_id'         => $withdrawal->id,
                            'is_waiting_withdrawal' => false
                        ]
                    );
                }
            }
        };

        $transactionsSum->chunkById(2000, $test);
        return $currentValue;
    }
}
