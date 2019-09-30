<?php

namespace Modules\Projects\Transformers;

use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ProjectsResource extends Resource {

    public function toArray($request) {

        return [
            'id' => Hashids::encode($this->id),
            'photo' => $this->photo,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => (new Carbon($this->created_at))->format('d/m/Y'),
            'shopify_id' => $this->shopify_id,
            'logo' => $this->logo,
            'url_page' => $this->url_page,
            'contact' => $this->contact,
            'support_phone' => $this->support_phone,
            'invoice_description' => $this->invoice_description,
            'installments_amount' => $this->installments_amount,
            'installments_interest_free' => $this->installments_interest_free,
            'boleto' => $this->boleto,
            'boleto_redirect' => $this->boleto_redirect,
            'card_redirect' => $this->card_redirect,
            'analyzing_redirect' => $this->analyzing_redirect,
            'shopify_id' => $this->shopify_id,
        ];
    }
}
