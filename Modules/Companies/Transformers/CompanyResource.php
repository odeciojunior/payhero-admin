<?php

namespace Modules\Companies\Transformers;

use Illuminate\Http\Request;
use Modules\Core\Entities\Company;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\CompanyService;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Core\Presenters\CompanyPresenter;
use Laracasts\Presenter\Exceptions\PresenterException;

/**
 * Class CompanyResource
 * @package Modules\Companies\Transformers
 */
class CompanyResource extends Resource
{
    /**
     * The resource instance.
     * @var Company
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     * @throws PresenterException
     */
    public function toArray($request)
    {
        $presenter = $this->resource->present();

        $documentStatus   = $presenter->allStatusPending() ? $presenter->getStatus(3) : $presenter->getStatus(1);
        $companyService   = new CompanyService();
        $refusedDocuments = $companyService->getRefusedDocuments($this->resource->id);
        $user             = $this->resource->user;

        return [
            'id_code'                       => Hashids::encode($this->resource->id),
            'business_website'              => $this->resource->business_website ?? '',
            'support_email'                 => $this->resource->support_email ?? '',
            'support_telephone'             => $this->resource->support_telephone ?? '',
            'fantasy_name'                  => $this->resource->fantasy_name ?? '',
            'company_document'              => strlen($this->resource->company_document) == 14 ? FoxUtils::mask($this->resource->company_document, '##.###.###/####-##') : (strlen($this->resource->company_document) == 11 ? FoxUtils::mask($this->resource->company_document, '###.###.###-##') : $this->resource->company_document),
            'zip_code'                      => $this->resource->zip_code ?? '',
            'country'                       => $this->resource->country ?? '',
            'country_translated'            => $this->resource->country ? __('definitions.enum.country.' . $this->resource->country) : '',
            'state'                         => $this->resource->state ?? '',
            'city'                          => $this->resource->city ?? '',
            'street'                        => $this->resource->street ?? '',
            'complement'                    => $this->resource->complement ?? '',
            'neighborhood'                  => $this->resource->neighborhood ?? '',
            'number'                        => $this->resource->number ?? '',
            'bank'                          => $this->resource->bank ?? '',
            'agency'                        => $this->resource->agency ?? '',
            'agency_digit'                  => $this->resource->agency_digit ?? '',
            'account'                       => $this->resource->account ?? '',
            'account_digit'                 => $this->resource->account_digit ?? '',
            'document_status'               => $documentStatus,
            'bank_document_status'          => $presenter->getBankDocumentStatus($this->resource->bank_document_status),
            'address_document_status'       => $presenter->getAddressDocumentStatus($this->resource->address_document_status),
            'contract_document_status'      => $presenter->getContractDocumentStatus($this->resource->contract_document_status),
            'bank_document_translate'       => __('definitions.enum.status.' . $presenter->getBankDocumentStatus()),
            'address_document_translate'    => __('definitions.enum.status.' . $presenter->getAddressDocumentStatus()),
            'contract_document_translate'   => __('definitions.enum.status.' . $presenter->getContractDocumentStatus()),
            'refusedDocuments'              => $refusedDocuments,
            'type'                          => $this->company_type,
            'type_company'                  => $presenter->getCompanyType($this->company_type),
            'user_address_document_status'  => $presenter->getAddressDocumentStatus($user->address_document_status),
            'user_personal_document_status' => $presenter->getAddressDocumentStatus($user->personal_document_status),
        ];
    }
}
