<?php

namespace Modules\Companies\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class CompanyCpfResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $presenter        = $this->resource->present();
        $documentStatus   = $presenter->allStatusPending() ? $presenter->getStatus(3) : $presenter->getStatus(1);
        $companyService   = new CompanyService();
        $refusedDocuments = $companyService->getRefusedDocuments($this->resource->id);

        return [
            'id_code'                     => Hashids::encode($this->resource->id),
            'bank'                        => $this->resource->bank ?? '',
            'agency'                      => $this->resource->agency ?? '',
            'agency_digit'                => $this->resource->agency_digit ?? '',
            'account'                     => $this->resource->account ?? '',
            'account_digit'               => $this->resource->account_digit ?? '',
            'document_status'             => $documentStatus,
            'bank_document_status'        => $this->resource->bank_document_status,
            'address_document_status'     => $this->resource->address_document_status,
            'contract_document_status'    => $this->resource->contract_document_status,
            'bank_document_translate'     => __('definitions.enum.status.' . $presenter->getBankDocumentStatus()),
            'refusedDocuments'            => $refusedDocuments,
            'type'                        => $this->company_type,
        ];
    }
}


