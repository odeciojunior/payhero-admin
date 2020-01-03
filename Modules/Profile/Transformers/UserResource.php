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
            'personal_document_translate' => $this->present()
                                                  ->getPersonalDocumentStatus($this->personal_document_status),
            'address_document_translate'  => $this->present()
                                                  ->getPersonalDocumentStatus($this->address_document_status),
            // Notificações
            'new_affiliation'             => empty($userNotification->new_affiliation) ? false : $userNotification->new_affiliation,
            'new_affiliation_request'     => empty($userNotification->new_affiliation_request) ? false : $userNotification->new_affiliation_request,
            'approved_affiliation'        => empty($userNotification->approved_affiliation) ? false : $userNotification->approved_affiliation,
            'boleto_compensated'          => empty($userNotification->boleto_compensated) ? false : $userNotification->boleto_compensated,
            'sale_approved'               => empty($userNotification->sale_approved) ? false : $userNotification->sale_approved,
            'notazz'                      => empty($userNotification->notazz) ? false : $userNotification->notazz,
            'withdrawal_approved'         => empty($userNotification->withdrawal_approved) ? false : $userNotification->withdrawal_approved,
            'released_balance'            => empty($userNotification->released_balance) ? false : $userNotification->released_balance,
            'domain_approved'             => empty($userNotification->domain_approved) ? false : $userNotification->domain_approved,
            'shopify'                     => empty($userNotification->shopify) ? false : $userNotification->shopify,
            'billet_generated'            => empty($userNotification->billet_generated) ? false : $userNotification->billet_generated,
            'credit_card_in_proccess'     => empty($userNotification->credit_card_in_proccess) ? false : $userNotification->credit_card_in_proccess,

            'refusedDocuments' => $refusedDocuments,
        ];
    }
}
