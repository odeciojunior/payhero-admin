<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Anticipation;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\User;

/**
 * Class AnticipationService
 * @package Modules\Core\Services
 */
class AnticipationService
{
    /**
     * @param Company $company
     * @return array
     */
    public function getAntecipationData(Company $company)
    {

        $antecipableValue = $this->getAntecipableValue($company);
        $antecipableTax   = $this->getAnticipationTax($company);

        return [
            'percetage_antecipable' => $company->user->percentage_antecipable,
            'antecipable_value'     => number_format($antecipableValue / 100, 2, ',', '.'),
            'tax_value'             => number_format($antecipableTax / 100, 2, ',', '.'),
            'value_minus_tax'       => number_format(($antecipableValue - $antecipableTax) / 100, 2, ',', '.')
        ];
    }

    /**
     * @param Company $company
     * @return int
     */
    public function getAntecipableValue(Company $company)
    {
        $antecipableTransactionsValue = $this->getQuery($company->id)->sum('value');

        return intval($antecipableTransactionsValue / 100 * $company->user->percentage_antecipable);
    }

    /**
     * @param Company $company
     * @return int
     */
    public function getAnticipationTax(Company $company)
    {
        $antecipableTransactions = $this->getQuery($company->id)->get();

        $dailyTax = $this->getDailyTax($company->user);

        $taxValue = 0;

        foreach ($antecipableTransactions as $anticipableTransaction) {
            $diffInDays = Carbon::now()->diffInDays($anticipableTransaction->release_date) + 1;

            $transactionPercentageTax = $diffInDays * $dailyTax;

            $taxValue += intval(intval($anticipableTransaction->value / 100 * $company->user->percentage_antecipable) / 100 * $transactionPercentageTax);
        }

        return (int) $taxValue;
    }

    /**
     * @param Company $company
     * @return int
     */
    public function performAnticipation(Company $company)
    {
        $anticipationModel           = new Anticipation;
        $anticipatedTransactionModel = new AnticipatedTransaction;
        $transferModel               = new Transfer;
        $transactionModel            = new Transaction;

        $antecipableTransactions = $this->getQuery($company->id)->get();

        if (count($antecipableTransactions) == 0) {
            return [
                'message'   => 'Você não tem saldo disponivel para antecipar!',
            ];
        }

        $user     = $company->user;
        $dailyTax = $this->getDailyTax($user);

        $taxValue          = $this->getAnticipationTax($company);
        $anticipationValue = $this->getAntecipableValue($company);
        $percentageTax     = 0;
        $anticipationArray = [];

        foreach ($antecipableTransactions as $anticipableTransaction) {
            $diffInDays = Carbon::now()->diffInDays($anticipableTransaction->release_date) + 1;

            $percentageTax = $diffInDays * $dailyTax;

            $anticipationArray[] = [
                'value'           => intval($anticipableTransaction->value / 100 * $company->user->percentage_antecipable),
                'tax_value'       => number_format(intval(intval($anticipableTransaction->value / 100 * $company->user->percentage_antecipable) / 100 * $percentageTax), 4, '.', ','),
                'tax'             => $percentageTax,
                'days_to_release' => $diffInDays,
                'transaction_id'  => $anticipableTransaction->id,
            ];

            $anticipableTransaction->update([
                'status'      => 'anticipated',
                'status_enum' => $transactionModel->present()->getStatusEnum('anticipated')
            ]);
        }

        $anticipation = $anticipationModel->create([
            'value'          => $anticipationValue - $taxValue,
            'tax'            => $taxValue,
            'percentage_tax' => $user->antecipation_tax,
            'company_id'     => $company->id,
        ]);

        foreach ($anticipationArray as $item) {
            $anticipatedTransactionModel->create([
                'anticipation_id' => $anticipation->id,
                'transaction_id'  => $item['transaction_id'],
                'value'           => $item['value'],
                'tax'             => $item['tax'],
                'tax_value'       => $item['tax_value'],
                'days_to_release' => $item['days_to_release'],
            ]);
        }

        $transferModel->create([
            'user_id'         => $user->id,
            'company_id'      => $company->id,
            'anticipation_id' => $anticipation->id,
            'value'           => $anticipation->value,
            'type'            => 'in',
            'type_enum'       => $transferModel->present()->getTypeEnum('in'),
            'reason'          => 'Antecipação',
        ]);

        $company->update([
            'balance' => $company->balance + $anticipation->value,
        ]);

        return [
            'message' => 'Saldo antecipado com successo!',
        ];
    }

    /**
     * @param Company $company
     * @return Builder
     */
    public function getQuery(int $companyId)
    {
        $transactionModel = new Transaction;
        
        return $transactionModel->with(['sale' => function ($query) {
                                    $query->where('payment_method', (new Sale)->present()->getPaymentType('credit_card'));
                                }])
                                ->where('company_id', $companyId)
                                ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
                                ->whereDate('release_date', '>', Carbon::today())
                                ->whereNull('invitation_id')
                                ->whereDoesntHave('anticipatedTransactions');
    }

    /**
     * @param User $user
     * @return float
     */
    public function getDailyTax(User $user)
    {
        return number_format(($user->antecipation_tax / $user->credit_card_release_money_days), 4, '.', ',');
    }

}
