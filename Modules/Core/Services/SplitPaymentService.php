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

class SplitPaymentService {


    public function splitPayment($totalValue, Sale $sale, Project $project, User $user) {

        try {
            $transactionModel          = new Transaction();
            $userModel                 = new User();
            $userProjectModel          = new UserProject();
            $companyModel              = new Company();
            $invitationModel           = new Invitation();
            $convertaxIntegrationModel = new ConvertaxIntegration();

            $cloudfoxValue = (int)(($totalValue  / 100) * $user->percentage_rate);
            $cloudfoxValue += str_replace('.', '', $user->transaction_rate);

            $producerValue = (int) $totalValue - $cloudfoxValue;

            $invite = $invitationModel->where([
                                                ['user_invited', $user->id],
                                                ['status', 1], 
                                            ])->with([
                                                'user', 'company'
                                            ])->first();

            if (!empty($invite) && !empty($invite->company) && !empty($invite->user)) {

                $valueProducerGodfather = intval(($producerValue / 100 * 1));

                $transactionModel->create([
                                        'sale_id'                => $sale->id,
                                        'company_id'             => $invite->company->id,
                                        'value'                  => $valueProducerGodfather,
                                        'release_date'           => null,
                                        'status'                 => 'pending',
                                        'antecipable_value'      => 0,
                                        'antecipable_tax'        => 0,
                                        'currency'               => '',
                                        'percentage_rate'        => '',
                                        'transaction_rate'       => '',
                                        'percentage_antecipable' => '',
                                        'invitation_id'          => $invite->id,
                                        'type'                   => $transactionModel->present()->getType('invitation')
                                    ]);

                $cloudfoxValue -= $valueProducerGodfather; 
            }

            $convertaxIntegration = $convertaxIntegrationModel->where('project_id', $project->id)->first();

            if(!empty($convertaxIntegration)){

                $convertaxUser = $userModel->find(27);
                $convertaxCompany = $companyModel->find(29);

                $transactionModel->create([
                    'sale_id'                => $sale->id,
                    'company_id'             => $convertaxCompany->id,
                    'value'                  => $convertaxIntegration->value,
                    'release_date'           => null,
                    'antecipation_date'      => null,
                    'status'                 => 'pending',
                    'antecipable_value'      => intval($convertaxIntegration->value / 100 * $convertaxUser->percentage_antecipable),
                    'antecipable_tax'        => $convertaxUser->antecipation_tax,
                    'currency'               => ($convertaxCompany->country == 'usa') ? 'dolar' : 'real',
                    'percentage_rate'        => $convertaxUser->percentage_rate,
                    'transaction_rate'       => ($convertaxCompany->country == 'usa') ? '0.25' : '1.00',
                    'percentage_antecipable' => $convertaxUser->percentage_antecipable,
                    'type'                   => $transactionModel->present()->getType('convertaX')
                ]);

                $producerValue -= $convertaxIntegration->value;
            }

            $userProject = $userProjectModel->where([
                ['type', 'producer'],
                ['project_id', $project->id],
            ])->first();

            $producerCompany = $userProject->company;

            $transactionModel->create([
                                    'sale_id'                => $sale->id,
                                    'company_id'             => $producerCompany->id,
                                    'value'                  => $producerValue,
                                    'release_date'           => null,
                                    'antecipation_date'      => null,
                                    'status'                 => 'pending',
                                    'antecipable_value'      => intval($producerValue / 100 * $user->percentage_antecipable),
                                    'antecipable_tax'        => $user->antecipation_tax,
                                    'currency'               => ($producerCompany->country == 'usa') ? 'dolar' : 'real',
                                    'percentage_rate'        => $user->percentage_rate,
                                    'transaction_rate'       => ($producerCompany->country == 'usa') ? '0.25' : '1.00',
                                    'percentage_antecipable' => $user->percentage_antecipable,
                                    'type'                   => $transactionModel->present()->getType('producer')
            ]);

            $transactionModel->create([
                                    'sale_id'  => $sale->id,
                                    'value'    => $cloudfoxValue,
                                    'status'   => 'pending',
                                    'currency' => ($producerCompany->country == 'usa') ? 'dolar' : 'real',
                                    'type'     => $transactionModel->present()->getType('producer')
                                ]);

        } catch (\Exception $e) {
            Log::critical('erro ao fazer split da venda ' . $sale->id);
            report($e);
        }

    }
}

