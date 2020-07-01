<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\Transaction;

/**
 * Class CompaniesService
 * @package Modules\Core\Services
 */
class CompanyService
{
    /**
     * @param bool $paginate
     * @return array|AnonymousResourceCollection
     * @var Company $companies
     */
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
                            'definitions.enum.company_document_type.' . $companyPresenter->getDocumentType(
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

    /**
     * @param $company
     * @return bool
     * Se os dados do relacionados ao banco forem alterados o status documento muda para pendente
     */
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

    /**
     * @param $cnpj
     * @return mixed|void
     */
    public function getNameCompanyByApiCNPJ($cnpj)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.receitaws.com.br/v1/cnpj/' . $cnpj);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            return json_decode($response, true);
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * @param Company $company
     * @return int|mixed
     * @throws PresenterException
     */
    public function getPendingBalance(Company $company)
    {
        $transactionModel = new Transaction();

        $pendingBalance = $transactionModel->where('company_id', $company->id)
            ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
            // ->whereDate('release_date', '>', Carbon::today()->toDateString())
            ->sum('value');

        $transactionsAnticipatedValue = $transactionModel->with('anticipatedTransactions')
            ->where('company_id', $company->id)
            ->where('status_enum', $transactionModel->present()->getStatusEnum('anticipated'))
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

    /**
     * @param Company $company
     * @return bool
     * Verifica campos que estao vazio para integração com getnet
     */
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
            if (empty($company->federal_registration_status)) {
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
        } elseif (empty($company->company_document)) {
            return true;
        } elseif (empty($company->bank)) {
            return true;
        } elseif (empty($company->agency)) {
            return true;
        } elseif (empty($company->account)) {
            return true;
        } elseif (empty($company->account_type)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Company $company
     * @return string[]
     * @throws PresenterException
     */
    public function createCompanyGetnet(Company $company)
    {
        $getnetService = new GetnetService();
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

        if (!empty($result) && $result['message'] == 'success') {
            $company->update(
                [
                    'subseller_getnet_id' => $result['data']->subseller_id,
                    'getnet_status' => $company->present()->getStatusGetnet('review')
                ]
            );

            return [
                'message' => 'success',
                'data' => ''
            ];
        }
        return [
            'message' => 'error',
            'data' => ''
        ];
    }

    /**
     * @param Company $company
     * @return string[]
     * @throws PresenterException
     */
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
            'data' => ''
        ];
    }
}
