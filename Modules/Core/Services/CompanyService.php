<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Ticket;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Tracking;
use DB;

/**
 * Class CompaniesService
 * @package Modules\Core\Services
 */
class CompanyService
{
    public function getCompaniesUser($paginate = false)
    {
        try {
            $companyModel = new Company();

            $companies = $companyModel->with('user')->where('user_id', auth()->user()->account_owner_id);

            if ($paginate) {
                return CompanyResource::collection($companies->orderBy('order_priority')->paginate(10));
            } else {
                return CompaniesSelectResource::collection($companies->orderBy('order_priority')->get());
            }
        } catch (Exception $e) {
            report($e);

            return [];
        }
    }

    public function isDocumentValidated(int $companyId)
    {
        $companyModel = new Company();
        $company = $companyModel->find($companyId);
        $companyPresenter = $companyModel->present();
        if (!empty($company)) {
            if ($company->company_type == $companyPresenter->getCompanyType('juridical person')) {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') &&
                    $company->address_document_status == $companyPresenter->getAddressDocumentStatus('approved') &&
                    $company->contract_document_status == $companyPresenter->getContractDocumentStatus('approved')) {
                    return true;
                }
            } else {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function haveAnyDocumentPending()
    {
        $companyModel = new Company();
        $companies = $companyModel->where('user_id', auth()->user()->account_owner_id)->get();
        $companyPresenter = $companyModel->present();

        foreach ($companies as $company) {
            if ($company->company_type == $companyPresenter->getCompanyType('juridical person')) {
                if (($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') ||
                        $company->bank_document_status == $companyPresenter->getBankDocumentStatus('analyzing')) &&
                    ($company->address_document_status == $companyPresenter->getAddressDocumentStatus('approved') ||
                        $company->address_document_status == $companyPresenter->getAddressDocumentStatus(
                            'analyzing'
                        )) &&
                    ($company->contract_document_status == $companyPresenter->getContractDocumentStatus('approved') ||
                        $company->contract_document_status == $companyPresenter->getContractDocumentStatus(
                            'analyzing'
                        ))) {
                    return false;
                }
            } else {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') ||
                    $company->bank_document_status == $companyPresenter->getBankDocumentStatus('analyzing')) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getRefusedDocuments(int $companyId)
    {
        $companyModel = new Company();
        $company = $companyModel->with('companyDocuments')->find($companyId);
        $companyPresenter = $companyModel->present();
        $refusedDocuments = collect();
        if (!empty($company)) {
            foreach ($company->companyDocuments as $document) {
                if (!empty($document->refused_reason)) {
                    $dataDocument = [
                        'date' => $document->created_at->format('d/m/Y'),
                        'type_translated' => __(
                            'definitions.enum.company_document_type.'.$companyPresenter->getDocumentType(
                                $document->document_type_enum
                            )
                        ),
                        'document_url' => $document->document_url,
                        'refused_reason' => $document->refused_reason,
                    ];
                    $refusedDocuments->push(collect($dataDocument));
                }
            }
        }

        return $refusedDocuments;
    }

    public function verifyCnpj($cnpj)
    {
        $companyModel = new Company();
        $companyPresenter = $companyModel->present();
        $cnpj = preg_replace("/[^0-9]/", "", $cnpj);
        $company = $companyModel->where(
            [
                ['company_document', $cnpj],
                ['bank_document_status', $companyPresenter->getBankDocumentStatus('approved')],
                ['address_document_status', $companyPresenter->getAddressDocumentStatus('approved')],
                ['contract_document_status', $companyPresenter->getContractDocumentStatus('approved')],
            ]
        )->first();
        if (!empty($company)) {
            return true;
        } else {
            return false;
        }
    }

    public function getChangesUpdateBankData($company)
    {
        $companyChanges = $company->getChanges();

        if ((!empty($companyChanges['bank']) || array_key_exists('bank', $companyChanges))
            || (!empty($companyChanges['agency']) || array_key_exists('agency', $companyChanges))
            || (!empty($companyChanges['account']) || array_key_exists('account', $companyChanges))
            || (!empty($companyChanges['agency_digit']) || array_key_exists('agency_digit', $companyChanges))
            || (!empty($companyChanges['account_digit']) || array_key_exists('account_digit', $companyChanges))
        ) {
            $company->update(
                [
                    'bank_document_status' => $company->present()->getStatus('pending'),
                ]
            );

            return true;
        }

        return false;
    }

    public function getChangesUpdateCNPJ($company, $documentType)
    {
        $companyChanges = $company->getChanges();

        if (!empty($documentType) && $documentType != $company->company_document) {
            $company->contract_document_status = $company->presenter()->getStatus('pending');
        }
    }

    public function getCurrency(Company $company, $symbol = false)
    {
        $dolar = [
            'usa',
        ];

        $euro = [
            'portugal',
            'germany',
            'spain',
            'france',
            'italy',
        ];

        $real = [
            'brazil',
            'brasil',
        ];

        if (in_array($company->country, $dolar)) {
            return $symbol ? '$' : 'dolar';
        } elseif (in_array($company->country, $euro)) {
            return $symbol ? '€' : 'euro';
        } elseif (in_array($company->country, $real)) {
            return $symbol ? 'R$' : 'real';
        } else {
            return null;
        }
    }

    public function getNameCompanyByApiCNPJ($cnpj)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.receitaws.com.br/v1/cnpj/'.$cnpj);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            return json_decode($response, true);
        } catch (Exception $e) {
            return;
        }
    }

    public function getPendingBalance(Company $company)
    {
        $transactionModel = new Transaction();

        $pendingBalance = $transactionModel->where('company_id', $company->id)
            ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
            // ->whereDate('release_date', '>', Carbon::today()->toDateString())
            ->sum('value');

        $transactionsAnticipatedValue = $transactionModel->with('anticipatedTransactions')
            ->where('company_id', $company->id)
            ->where('status_enum', $transactionModel->present()
                ->getStatusEnum('anticipated'))
            ->sum('value');

        $anticipatedValue = AnticipatedTransaction::with('transaction')->whereHas(
            'transaction',
            function ($query) use ($company) {
                $query->where('status_enum', (new Transaction())->present()->getStatusEnum('anticipated'));
                $query->where('company_id', $company->id);
            }
        )->sum('value');

        $pendingBalance += ($transactionsAnticipatedValue - $anticipatedValue);

        return $pendingBalance;
    }

    public function verifyFieldsEmpty(Company $company)
    {
        if ($company->company_type == $company->present()->getCompanyType('juridical person')) {
            // informações basicas
            if (empty($company->zip_code)) {
                return true;
            }
            if (empty($company->street)) {
                return true;
            }
            if (empty($company->neighborhood)) {
                return true;
            }
            if (empty($company->state)) {
                return true;
            }
            if (empty($company->city)) {
                return true;
            }
            if (empty($company->country)) {
                return true;
            }
            // informações complementares
            if (empty($company->patrimony)) {
                return true;
            }
            if (empty($company->state_fiscal_document_number)) {
                return true;
            }
            if (empty($company->business_entity_type)) {
                return true;
            }
            if (empty($company->economic_activity_classification_code)) {
                return true;
            }
            if (empty($company->monthly_gross_income)) {
                return true;
            }
            if (empty($company->founding_date)) {
                return true;
            }
            if (empty($company->federal_registration_status_date)) {
                return true;
            }
            if (empty($company->social_value)) {
                return true;
            }
            if (empty($company->document_number)) {
                return true;
            }
            if (empty($company->document_issue_date)) {
                return true;
            }
            if (empty($company->document_issuer)) {
                return true;
            }
            if (empty($company->document_issuer_state)) {
                return true;
            }
        }

        if (empty($company->fantasy_name)) {
            return true;
        }
        if (empty($company->company_document)) {
            return true;
        }
        if (empty($company->bank)) {
            return true;
        }
        if (empty($company->agency)) {
            return true;
        }
        if (empty($company->account)) {
            return true;
        }
        return false;
    }

    public function createCompanyGetnet(Company $company)
    {
        try {
            $getnetService = new GetnetBackOfficeService();
            $userService = new UserService();

            $user = $company->user;

            if (($company->present()->getCompanyType($company->company_type) == 'physical person')
                && (!$userService->verifyFieldsEmpty($user))
            ) {
                $result = $getnetService->createPfCompany($company);
            } elseif (($company->present()->getCompanyType($company->company_type) == 'juridical person')
                && !empty($user->cellphone) && !empty($user->email)) {
                $result = $getnetService->createPjCompany($company);
            }

            if (empty($result) || empty(json_decode($result)->subseller_id)) {
                return [
                    'message' => 'error',
                    'data' => '',
                ];
            }

            $company->update(
                [
                    'subseller_getnet_id' => json_decode($result)->subseller_id,
                    'get_net_status' => $company->present()->getStatusGetnet('review'),
                ]
            );

            return [
                'message' => 'success',
                'data' => '',
            ];
        } catch (Exception $e) {
            report($e);

            return [
                'message' => 'error',
                'data' => '',
            ];
        }
    }

    public function updateCompanyGetnet(Company $company)
    {
        $getnetService = new GetnetService();
        $userService = new UserService();
        $user = $company->user;

        if ($company->present()->getCompanyType($company->company_type) == 'physical person'
            && (!$userService->verifyFieldsEmpty($user))
        ) {
            $getnetService->updatePfCompany($company);
        } elseif (!empty($user->cellphone) && !empty($user->email)) {
            $getnetService->updatePjCompany($company);
        }

        return [
            'message' => 'success',
            'data' => '',
        ];
    }

    public function unfilledFields(Company $company)
    {
        $arrayFields = [];
        if ($company->company_type == $company->present()->getCompanyType('juridical person')) {
            // informações basicas
            if (empty($company->zip_code)) {
                $arrayFields[] = 'zip_code';
            }
            if (empty($company->street)) {
                $arrayFields[] = 'street';
            }
            if (empty($company->neighborhood)) {
                $arrayFields[] = 'neighborhood';
            }
            if (empty($company->state)) {
                $arrayFields[] = 'state';
            }
            if (empty($company->city)) {
                $arrayFields[] = 'city';
            }
            if (empty($company->country)) {
                $arrayFields[] = 'country';
            }
            // informações complementares
            if (empty($company->patrimony)) {
                $arrayFields[] = 'patrimony';
            }
            if (empty($company->state_fiscal_document_number)) {
                $arrayFields[] = 'state_fiscal_document_number';
            }
            if (empty($company->business_entity_type)) {
                $arrayFields[] = 'business_entity_type';
            }
            if (empty($company->economic_activity_classification_code)) {
                $arrayFields[] = 'economic_activity_classification_code';
            }
            if (empty($company->monthly_gross_income)) {
                $arrayFields[] = 'monthly_gross_income';
            }
            if (empty($company->founding_date)) {
                $arrayFields[] = 'founding_date';
            }
            if (empty($company->federal_registration_status_date)) {
                $arrayFields[] = 'federal_registration_status_date';
            }
            if (empty($company->social_value)) {
                $arrayFields[] = 'social_value';
            }
            if (empty($company->document_number)) {
                $arrayFields[] = 'document_number';
            }
            if (empty($company->document_issue_date)) {
                $arrayFields[] = 'document_issue_date';
            }
            if (empty($company->document_issuer)) {
                $arrayFields[] = 'document_issuer';
            }
            if (empty($company->document_issuer_state)) {
                $arrayFields[] = 'document_issuer_state';
            }
//            if (empty($company->account_type)) {
//                $arrayFields[] = 'account_type';
//            }
        } else {
            if (empty($company->fantasy_name)) {
                $arrayFields[] = 'fantasy_name';
            }
            if (empty($company->company_document)) {
                $arrayFields[] = 'company_document';
            }
            if (empty($company->bank)) {
                $arrayFields[] = 'bank';
            }
            if (empty($company->agency)) {
                $arrayFields[] = 'agency';
            }
            if (empty($company->account)) {
                $arrayFields[] = 'account';
            }
//            if (empty($company->account_type)) {
//                $arrayFields[] = 'account_type';
//            }
        }

        return $arrayFields;
    }

    public function getBlockedBalance(int $companyId, int $userAccountOwnerId)
    {
        /* $salesModel = new Sale();
         $ticketModel = new Ticket();

         return $salesModel->join('transactions', 'transactions.sale_id', '=', 'sales.id')
             ->where('sales.owner_id', $userAccountOwnerId)
             ->where('sales.status', $salesModel->present()->getStatus('approved'))
             ->whereNull('transactions.invitation_id')
             ->where('transactions.company_id', $companyId)
             ->whereHas('tickets', function ($query) use ($ticketModel) {
                 $query->where('ticket_status_enum', $ticketModel->present()
                     ->getTicketStatusEnum('mediation'))
                     ->where('ignore_balance_block', 0);
             })->sum('transactions.value');*/

        $salesModel = new Sale();
        $transactiosModel = new Transaction();

        return $salesModel->join('transactions', 'transactions.sale_id', '=', 'sales.id')
            ->where('sales.owner_id', $userAccountOwnerId)
            // ->where('sales.status', $salesModel->present()->getStatus('in_dispute'))
            ->whereNull('transactions.invitation_id')
            ->where('transactions.company_id', $companyId)
            ->whereIn('transactions.status_enum', collect([
                $transactiosModel->present()->getStatusEnum('transfered'),
                $transactiosModel->present()->getStatusEnum('paid')
            ]))
            ->where(function($queryDispute) use($salesModel){
                $queryDispute->where('sales.status', $salesModel->present()->getStatus('in_dispute'))
                             ->orWhere(function($queryTracking) use($salesModel) {
                                    $queryTracking->where('sales.status', $salesModel->present()->getStatus('approved'))
                                       ->where(function ($query) {
                                            $query->whereHas('tracking', function ($trackingsQuery) {
                                                $trackingPresenter = (new Tracking)->present();
                                                $status = [
                                                    $trackingPresenter->getSystemStatusEnum('unknown_carrier'),
                                                    $trackingPresenter->getSystemStatusEnum('no_tracking_info'),
                                                    $trackingPresenter->getSystemStatusEnum('posted_before_sale'),
                                                    $trackingPresenter->getSystemStatusEnum('duplicated'),
                                                ];
                                                $trackingsQuery->whereIn('system_status_enum', $status);
                                            })->orDoesntHave('tracking');
                                        });
                            });
            })
            ->select(\DB::raw(
                'SUM(CASE WHEN transactions.status_enum = 1 THEN transactions.value ELSE 0 END) as transfered,
                 SUM(CASE WHEN transactions.status_enum = 2 AND sales.status = 24 THEN transactions.value ELSE 0 END) as pending'
            ))
            ->first();
    }
}
