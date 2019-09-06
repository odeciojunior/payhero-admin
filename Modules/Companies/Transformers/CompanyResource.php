<?php

namespace Modules\Companies\Transformers;

use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class CompanyResource
 * @package Modules\Companies\Transformers
 */
class CompanyResource extends Resource
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id_code'                     => Hashids::encode($this->id),
            'business_website'            => $this->business_website ?? '',
            'support_email'               => $this->support_email ?? '',
            'support_telephone'           => $this->support_telephone ?? '',
            'fantasy_name'                => $this->fantasy_name ?? '',
            'company_document'            => $this->company_document ?? '',
            'zip_code'                    => $this->zip_code ?? '',
            'country'                     => $this->country ?? '',
            'state'                       => $this->state ?? '',
            'city'                        => $this->city ?? '',
            'street'                      => $this->street ?? '',
            'complement'                  => $this->complement ?? '',
            'neighborhood'                => $this->neighborhood ?? '',
            'number'                      => $this->number ?? '',
            'bank'                        => $this->bank ?? '',
            'agency'                      => $this->agency ?? '',
            'agency_digit'                => $this->agency_digit ?? '',
            'account'                     => $this->account ?? '',
            'account_digit'               => $this->account_digit ?? '',
            'document_status'             => $this->bank_document_status == 3 && $this->address_document_status == 3 && $this->contract_document_status == 3 ? $this->present()->getStatus(3) : $this->present()->getStatus(1),
            'bank_document_status'        => $this->bank_document_status,
            'address_document_status'     => $this->address_document_status,
            'contract_document_status'    => $this->contract_document_status,
            'bank_document_translate'     => Lang::get('definitions.enum.status.' . $this->present()->getBankDocumentStatus($this->bank_document_status)),
            'address_document_translate'  => Lang::get('definitions.enum.status.' . $this->present()->getAddressDocumentStatus($this->address_document_status)),
            'contract_document_translate' => Lang::get('definitions.enum.status.' . $this->present()->getContractDocumentStatus($this->contract_document_status)),
        ];
    }
}
