<?php

namespace Modules\Profile\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\UserService;

/**
 * Class UserResource
 * @package Modules\Profile\Transformers
 */
class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $userNotification = $this->userNotification ?? collect();
        $userService      = new UserService();
        $refusedDocuments = $userService->getRefusedDocuments();

        return [
            'id_code'                     => $this->id_code,
            'name'                        => $this->name,
            'email'                       => $this->email,
            'email_verified'              => $this->email_verified,
            'cellphone'                   => $this->cellphone,
            'cellphone_verified'          => $this->cellphone_verified,
            'document'                    => $this->document,
            'zip_code'                    => $this->zip_code,
            'country'                     => $this->country,
            'state'                       => $this->state,
            'city'                        => $this->city,
            'neighborhood'                => $this->neighborhood,
            'street'                      => $this->street,
            'number'                      => $this->number,
            'complement'                  => $this->complement,
            'photo'                       => $this->photo,
            'date_birth'                  => $this->date_birth,
            'personal_document_status'    => $this->personal_document_status,
            'address_document_status'     => $this->address_document_status,
            'personal_document_translate' => Lang::get('definitions.enum.personal_document_status.' . $this->present()
                                                                                                           ->getPersonalDocumentStatus($this->personal_document_status)),
            'address_document_translate'  => Lang::get('definitions.enum.personal_document_status.' . $this->present()
                                                                                                           ->getPersonalDocumentStatus($this->address_document_status)),
            // Notificações
            'new_affiliation'             => $userNotification->new_affiliation ?? false,
            'new_affiliation_request'     => $userNotification->new_affiliation_request ?? false,
            'approved_affiliation'        => $userNotification->approved_affiliation ?? false,
            'boleto_compensated'          => $userNotification->boleto_compensated ?? false,
            'sale_approved'               => $userNotification->sale_approved ?? false,
            'notazz'                      => $userNotification->notazz ?? false,
            'withdrawal_approved'         => $userNotification->withdrawal_approved ?? false,
            'released_balance'            => $userNotification->released_balance ?? false,
            'domain_approved'             => $userNotification->domain_approved ?? false,
            'shopify'                     => $userNotification->shopify ?? false,
            'billet_generated'            => $userNotification->billet_generated ?? false,
            'credit_card_in_proccess'     => $userNotification->credit_card_in_proccess ?? false,

            'refusedDocuments' => $refusedDocuments,
        ];
    }
}
