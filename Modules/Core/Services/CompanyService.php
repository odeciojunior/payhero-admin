<?php

namespace Modules\Core\Services;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Modules\Companies\Transformers\CompaniesSelectResource;
use Modules\Companies\Transformers\CompanyResource;
use Modules\Core\Entities\Company;

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
            if ($company->bank_document_status == $companyPresenter->getBankDocumentStatus('approved') &&
                $company->address_document_status == $companyPresenter->getAddressDocumentStatus('approved') &&
                $company->contract_document_status == $companyPresenter->getContractDocumentStatus('approved')) {
                return true;
            } else {
                return false;
            }
        }

        return false;
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
}
