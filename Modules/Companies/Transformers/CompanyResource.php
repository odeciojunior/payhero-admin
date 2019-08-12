<?php

namespace Modules\Companies\Transformers;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class CompanyResource
 * @package Modules\Companies\Transformers
 */
class CompanyResource extends Resource
{
    /**
     * @return int
     */
    private function documentStatus()
    {
        //TODO criar logica para trazer o status correto
        if ($this->bank_document_status == 3 && $this->address_document_status == 3 && $this->contract_document_status == 3) {
            return $this->getEnum('status', 3, true);
        } else {
            return $this->getEnum('status', 1, true);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id_code'                     => $this->id_code,
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
            'document_status'             => $this->documentStatus(),
            'bank_document_status'        => $this->bank_document_status,
            'address_document_status'     => $this->address_document_status,
            'contract_document_status'    => $this->contract_document_status,
            'bank_document_translate'     => $this->getEnum('bank_document_status', $this->bank_document_status, true),
            'address_document_translate'  => $this->getEnum('address_document_status', $this->address_document_status, true),
            'contract_document_translate' => $this->getEnum('contract_document_status', $this->contract_document_status, true),
        ];
    }
}
