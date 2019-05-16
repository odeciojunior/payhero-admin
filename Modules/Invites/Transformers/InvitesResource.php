<?php

namespace Modules\Invites\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class InvitesResource extends Resource {

    public function toArray($request) {

        return [
            'email_invited'   => $this->email_invited,
            'status'          => $this->status,
            'register_date'   => $this->register_date,
            'expiration_date' => $this->expiration_date,
        ];
    }
}
