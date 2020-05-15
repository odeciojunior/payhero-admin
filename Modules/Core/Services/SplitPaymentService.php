<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\UserProject;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\ConvertaxIntegration;

/**
 * Class SplitPaymentService
 * @package Modules\Core\Services
 */
class SplitPaymentService
{
    /**
     * @param $totalValue
     * @param Sale $sale
     * @param Project $project
     * @param User $user
     */
    public function splitPayment($totalValue, Sale $sale, Project $project, User $user)
    {

        try {
            $transactionModel          = new Transaction();
            $userModel                 = new User();
            $userProjectModel          = new UserProject();
            $companyModel              = new Company();
            $invitationModel           = new Invitation();
            $convertaxIntegrationModel = new ConvertaxIntegration();
            if ($sale->payment_method == 1) {
                $percentageRate = $user->credit_card_tax;
            } else if ($sale->payment_method == 2) {
                $percentageRate = $user->boleto_tax;
            } else if ($sale->payment_method == 3) {
                $percentageRate = $user->debit_card_tax;
            } else {
                $percentageRate = 6.5;
            }
            $cloudfoxValue = (int) (($totalValue / 100) * $percentageRate);
            $cloudfoxValue += str_replace('.', '', $user->transaction_rate);

            $producerValue = (int) $totalValue - $cloudfoxValue;

            //Parceiro Projeto
            $partners = $userProjectModel->where([
                                                     ['project_id', $project->id],
                                                     [
                                                         'type_enum', '!=', $userProjectModel->present()
                                                                                             ->getTypeEnum('producer'),
                                                     ],
                                                 ])->get();

            if (count($partners) > 0) {
                $shippingPrice       = $sale->shipment_value;
                $antecipationDaysKey = 'boleto_antecipation_money_days';
                $partnersValue       = 0;

                foreach ($partners as $partner) {

                    $partnerValue = intval((($producerValue - $shippingPrice) / 100) * $partner->remuneration_value);

                    $partnersValue += $partnerValue;

                    $partnerGodfather = $invitationModel->where([
                                                                    ['user_invited', $partner->user->id],
                                                                    ['status', 'Ativo'],
                                                                ])->first();

                    if (!empty($partnerGodfather)) {

                        $valueGodfatherPartner = intval((($partnerValue / 100) * 1));

                        $antecipableValue = intval(($valueGodfatherPartner / 100) * $partnerGodfather->user->antecipation_tax);

                        $transactionModel->create([
                                                      'sale_id'                => $sale->id,
                                                      'company_id'             => $partnerGodfather->company->id,
                                                      'value'                  => $valueGodfatherPartner,
                                                      'release_date'           => Carbon::now()
                                                                                        ->addDays($partnerGodfather->user->release_money_days)
                                                                                        ->format('Y-m-d'),
                                                      'antecipation_date'      => Carbon::now()
                                                                                        ->addDays($partnerGodfather->user->{$antecipationDaysKey})
                                                                                        ->format('Y-m-d'),
                                                      'status'                 => 'pending',
                                                      'status_enum'            => $transactionModel->present()
                                                                                                   ->getStatusEnum('pending'),
                                                      'antecipable_value'      => $antecipableValue,
                                                      'antecipable_tax'        => $partnerGodfather->user->antecipation_tax,
                                                      'currency'               => '',
                                                      'percentage_rate'        => '',
                                                      'transaction_rate'       => '',
                                                      'percentage_antecipable' => '',
                                                      'type'                   => $transactionModel->present()
                                                                                                   ->getType('invitation'),
                                                  ]);

                        $cloudfoxValue -= $valueGodfatherPartner;
                    }

                    $transactionModel->create([
                                                  'sale_id'                => $sale->id,
                                                  'company_id'             => $partner->company->id,
                                                  'value'                  => $partnerValue,
                                                  'release_date'           => Carbon::now()
                                                                                    ->addDays($partner->user->release_money_days)
                                                                                    ->format('Y-m-d'),
                                                  'antecipation_date'      => Carbon::now()
                                                                                    ->addDays($partner->user->{$antecipationDaysKey})
                                                                                    ->format('Y-m-d'),
                                                  'status'                 => 'pending',
                                                  'status_enum'            => $transactionModel->present()
                                                                                               ->getStatusEnum('pending'),
                                                  'antecipable_value'      => intval($partnerValue / 100 * $partner->user->percentage_antecipable),
                                                  'antecipable_tax'        => $partner->user->antecipation_tax,
                                                  'currency'               => '',
                                                  'percentage_rate'        => '',
                                                  'transaction_rate'       => '',
                                                  'percentage_antecipable' => '',
                                                  'type'                   => $transactionModel->present()
                                                                                               ->getType('partner'),
                                              ]);
                }

                $producerValue -= $partnersValue;
            }

            // Indicação
            $invite = $invitationModel->where([
                                                  ['user_invited', $user->account_owner_id],
                                                  ['status', 1],
                                              ])->with([
                                                           'company',
                                                       ])->first();

            if (!empty($invite) && !empty($invite->company)) {

                $valueProducerGodfather = intval(($producerValue / 100 * 1));

                $transactionModel->create([
                                              'sale_id'                => $sale->id,
                                              'company_id'             => $invite->company->id,
                                              'value'                  => $valueProducerGodfather,
                                              'release_date'           => null,
                                              'status'                 => 'pending',
                                              'status_enum'            => $transactionModel->present()
                                                                                           ->getStatusEnum('pending'),
                                              'antecipable_value'      => 0,
                                              'antecipable_tax'        => 0,
                                              'currency'               => '',
                                              'percentage_rate'        => '',
                                              'transaction_rate'       => '',
                                              'percentage_antecipable' => '',
                                              'invitation_id'          => $invite->id,
                                              'type'                   => $transactionModel->present()
                                                                                           ->getType('invitation'),
                                          ]);

                $cloudfoxValue -= $valueProducerGodfather;
            }
            //ConvertaX
            $convertaxIntegration = $convertaxIntegrationModel->where('project_id', $project->id)->first();

            if (!empty($convertaxIntegration)) {

                $convertaxUser    = $userModel->find(27);
                $convertaxCompany = $companyModel->find(29);
                if ($sale->payment_method == 1) {
                    $convertaXReleaseMoneyDays = $convertaxUser->credit_card_release_money_days;
                    $convertaXPercentageRate   = $convertaxUser->credit_card_tax;
                } else if ($sale->payment_method == 2) {
                    $convertaXReleaseMoneyDays = $convertaxUser->boleto_release_money_days;
                    $convertaXPercentageRate   = $convertaxUser->boleto_tax;
                } else if ($sale->payment_method == 3) {
                    $convertaXReleaseMoneyDays = $convertaxUser->debit_card_release_money_days;
                    $convertaXPercentageRate   = $convertaxUser->debit_card_tax;
                } else {
                    $convertaXPercentageRate = 6.5;
                }
                $transactionModel->create([
                                              'sale_id'                => $sale->id,
                                              'company_id'             => $convertaxCompany->id,
                                              'value'                  => $convertaxIntegration->value,
                                              'release_date'           => null,
                                              'antecipation_date'      => null,
                                              'status'                 => 'pending',
                                              'status_enum'            => $transactionModel->present()
                                                                                           ->getStatusEnum('pending'),
                                              'antecipable_value'      => intval($convertaxIntegration->value / 100 * $convertaxUser->percentage_antecipable),
                                              'antecipable_tax'        => $convertaxUser->antecipation_tax,
                                              'currency'               => ($convertaxCompany->country == 'usa') ? 'dolar' : 'real',
                                              'percentage_rate'        => $convertaXPercentageRate,
                                              'transaction_rate'       => ($convertaxCompany->country == 'usa') ? '0.25' : '1.00',
                                              'percentage_antecipable' => $convertaxUser->percentage_antecipable,
                                              'type'                   => $transactionModel->present()
                                                                                           ->getType('convertaX'),
                                          ]);

                $producerValue -= $convertaxIntegration->value;
            }

            $userProject = $userProjectModel->where([
                                                        [
                                                            'type_enum', $userProjectModel->present()
                                                                                          ->getTypeEnum('producer'),
                                                        ],
                                                        ['project_id', $project->id],
                                                    ])->first();
            //Restante do producer
            $producerCompany = $userProject->company;
            if ($producerCompany->country == 'usa') {
                $transactionRate = '0.25';
            } else {
                if ($producerValue <= 4000 && $sale->payment_method == 2) {
                    $transactionRate = 300;
                } else {
                    $transactionRate = $user->transaction_rate;
                }
            }
            $transactionModel->create([
                                          'sale_id'                => $sale->id,
                                          'company_id'             => $producerCompany->id,
                                          'value'                  => $producerValue,
                                          'release_date'           => null,
                                          'antecipation_date'      => null,
                                          'status'                 => 'pending',
                                          'status_enum'            => $transactionModel->present()
                                                                                       ->getStatusEnum('pending'),
                                          'antecipable_value'      => intval($producerValue / 100 * $user->percentage_antecipable),
                                          'antecipable_tax'        => $user->antecipation_tax,
                                          'currency'               => ($producerCompany->country == 'usa') ? 'dolar' : 'real',
                                          'percentage_rate'        => $percentageRate,
                                          'transaction_rate'       => $transactionRate,
                                          'percentage_antecipable' => $user->percentage_antecipable,
                                          'type'                   => $transactionModel->present()->getType('producer'),
                                      ]);
            //Valor da Fox
            $transactionModel->create([
                                          'sale_id'     => $sale->id,
                                          'value'       => $cloudfoxValue,
                                          'status'      => 'pending',
                                          'status_enum' => $transactionModel->present()
                                                                            ->getStatusEnum('pending'),
                                          'currency'    => ($producerCompany->country == 'usa') ? 'dolar' : 'real',
                                          'type'        => $transactionModel->present()->getType('cloudfox'),
                                      ]);
        } catch (\Exception $e) {
            Log::critical('erro ao fazer split da venda ' . $sale->id);
            report($e);
        }
    }
}

