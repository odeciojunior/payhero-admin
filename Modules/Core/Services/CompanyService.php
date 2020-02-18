<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyDocument;

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
                return CompanyResource::collection($companies->paginate(10));
            } else {
                return CompaniesSelectResource::collection($companies->get());
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar companies (CompaniesService - getCompaniesUser)');
            report($e);

            return [];
        }
    }

    public function isDocumentValidated(int $companyId)
    {
        $companyModel     = new Company();
        $company          = $companyModel->find($companyId);
        $companyPresenter = $companyModel->present();
        if (!empty($company)) {
            if ($company->company_type == $companyPresenter->getCompanyType('juridical person')) {
                if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') &&
                    $company->address_document_status == $companyPresenter->getAddressDocumentStatus('approved') &&
                    $company->contract_document_status == $companyPresenter->getContractDocumentStatus('approved')) {
                    return true;
                }
            } else if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved')) {
                return true;
            }
        }

        return false;
    }

    public function haveAnyDocumentPending()
    {
        $companyModel     = new Company();
        $companies        = $companyModel->where('user_id', auth()->user()->account_owner_id)->get();
        $companyPresenter = $companyModel->present();

        foreach ($companies as $company) {
            if ($company->company_type == $companyPresenter->getCompanyType('juridical person')) {
                if (($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') ||
                        $company->bank_document_status == $companyPresenter->getBankDocumentStatus('analyzing')) &&
                    ($company->address_document_status == $companyPresenter->getAddressDocumentStatus('approved') ||
                        $company->address_document_status == $companyPresenter->getAddressDocumentStatus('analyzing')) &&
                    ($company->contract_document_status == $companyPresenter->getContractDocumentStatus('approved') ||
                        $company->contract_document_status == $companyPresenter->getContractDocumentStatus('analyzing'))) {
                    return false;
                }
            } else if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') ||
                $company->bank_document_status == $companyPresenter->getBankDocumentStatus('analyzing')) {
                return false;
            }
        }

        return true;
    }

    public function getRefusedDocuments(int $companyId)
    {
        $companyModel     = new Company();
        $company          = $companyModel->with('companyDocuments')->find($companyId);
        $companyPresenter = $companyModel->present();
        $refusedDocuments = collect();
        if (!empty($company)) {
            foreach ($company->companyDocuments as $document) {
                if (!empty($document->refused_reason)) {
                    $dataDocument = [
                        'date'            => $document->created_at->format('d/m/Y'),
                        'type_translated' => __('definitions.enum.company_document_type.' . $companyPresenter->getDocumentType($document->document_type_enum)),
                        'document_url'    => $document->document_url,
                        'refused_reason'  => $document->refused_reason,
                    ];
                    $refusedDocuments->push(collect($dataDocument));
                }
            }
        }

        return $refusedDocuments;
    }

    public function verifyCnpj($cnpj)
    {
        $companyModel     = new Company();
        $companyPresenter = $companyModel->present();
        $cnpj             = preg_replace("/[^0-9]/", "", $cnpj);
        $company          = $companyModel->where(
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
            $company->update([
                                 'bank_document_status' => $company->present()->getStatus('pending'),
                             ]);
        }
    }

    public function getChangesUpdateCNPJ($company, $documentType)
    {
        $companyChanges = $company->getChanges();

        if (!empty($documentType) && $documentType != $company->company_document) {
            $company->contract_document_status = $company->presenter()->getStatus('pending');
        }
    }

    public function getCurrency(Company $company, $symbol = false){

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

        if(in_array($company->country, $dolar)){
            return $symbol ? '$' : 'dolar';
        }
        elseif(in_array($company->country, $euro)){
            return $symbol ? '€' : 'euro';
        }
        elseif(in_array($company->country, $real)){
            return $symbol ? 'R$' :'real';
        }
        else{
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
}
