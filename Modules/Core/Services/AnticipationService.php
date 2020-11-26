<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Anticipation;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
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

        $antecipableTax   = $this->getAnticipationTax($company);

        return [
            'tax_value'             => number_format($antecipableTax / 100, 2, ',', '.'),
        ];
    }

    /**
     * @param Company $company
     * @return int
     */
    public function performAnticipation(Company $company)
    {
//        $anticipationModel           = new Anticipation;
//        $anticipatedTransactionModel = new AnticipatedTransaction;
//        $transferModel               = new Transfer;
//        $transactionModel            = new Transaction;
//
//        $antecipableTransactions = $this->getQuery($company->id)->get();
//
//        if (count($antecipableTransactions) == 0) {
//            return [
//                'message'   => 'Você não tem saldo disponivel para antecipar!',
//            ];
//        }
//
//        $user     = $company->user;
//        $dailyTax = $this->getDailyTax($user);
//
//        $anticipationArray = [];
//
//        foreach ($antecipableTransactions as $anticipableTransaction) {
//            $diffInDays = Carbon::now()->diffInDays($anticipableTransaction->release_date) + 1;
//
//            $percentageTax = $diffInDays * $dailyTax;
//
//            $anticipationArray[] = [
//                'tax'             => $percentageTax,
//                'days_to_release' => $diffInDays,
//                'transaction_id'  => $anticipableTransaction->id,
//            ];
//
//            $anticipableTransaction->update([
//                'status'      => 'anticipated',
//                'status_enum' => $transactionModel->present()->getStatusEnum('anticipated')
//            ]);
//        }
//
//        $anticipation = $anticipationModel->create([
//            'percentage_tax'         => $user->antecipation_tax,
//            'company_id'             => $company->id,
//        ]);
//
//        foreach ($anticipationArray as $item) {
//            $anticipatedTransactionModel->create([
//                'anticipation_id' => $anticipation->id,
//                'transaction_id'  => $item['transaction_id'],
//                'value'           => $item['value'],
//                'tax'             => $item['tax'],
//                'tax_value'       => $item['tax_value'],
//                'days_to_release' => $item['days_to_release'],
//            ]);
//        }
//
//        $transferModel->create([
//            'user_id'         => $user->id,
//            'company_id'      => $company->id,
//            'anticipation_id' => $anticipation->id,
//            'value'           => $anticipation->value,
//            'type'            => 'in',
//            'type_enum'       => $transferModel->present()->getTypeEnum('in'),
//            'reason'          => 'Antecipação',
//        ]);
//
//        $company->update([
//            'balance' => $company->balance + $anticipation->value,
//        ]);
//
//        return [
//            'message' => 'Saldo antecipado com successo!',
//        ];
    }

    /**
     * @param  int  $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function getQuery(int $companyId)
    {
        $transactionModel = new Transaction;

        return $transactionModel->with([
            'sale' => function ($query) {
                $query->where('payment_method', (new Sale)->present()->getPaymentType('credit_card'));
            }
        ])->where('company_id', $companyId)
            ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
            ->whereDate('release_date', '>', Carbon::today())
            ->whereNull('invitation_id')
            ->whereDoesntHave('anticipatedTransactions')
            ->whereHas('productPlanSales', function ($query) {
                $query->whereHas('tracking', function ($trackingsQuery) {
                    $trackingPresenter = (new Tracking())->present();
                    $status = [
                        $trackingPresenter->getSystemStatusEnum('valid'),
                        $trackingPresenter->getSystemStatusEnum('checked_manually'),
                    ];
                    $trackingsQuery->whereIn('system_status_enum', $status);
                });
            });

    }
}
