<?php

namespace Modules\Invites\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;

class InviteResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => Hashids::encode($this->id),
            'email_invited'     => $this->email_invited,
            'status'            => $this->status,
            'status_translated' => Lang::get('definitions.enum.invitation.status.' . $this->present()
                                                                                          ->getStatus($this->status)),
            'register_date'     => ($this->register_date != '') ? date('d/m/Y', strtotime($this->register_date)) : 'Pendente',
            'expiration_date'   => ($this->expiration_date != '') ? date('d/m/Y', strtotime($this->expiration_date)) : 'Pendente',
            'company_name'      => $this->company->fantasy_name ?? '',
        ];
    }
}
