<?php

namespace Modules\Projects\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

/**
 * @property mixed id
 * @property mixed photo
 * @property mixed name
 * @property mixed description
 * @property mixed created_at
 * @property mixed shopify_id
 * @property mixed logo
 * @property mixed url_page
 * @property mixed contact
 * @property mixed support_phone
 * @property mixed invoice_description
 * @property mixed installments_amount
 * @property mixed installments_interest_free
 * @property mixed boleto_redirect
 * @property mixed card_redirect
 * @property mixed analyzing_redirect
 * @property mixed boleto
 * Class ProjectsResource
 * @package Modules\Projects\Transformers
 */
class ProjectsResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        return [
            'id'                         => Hashids::encode($this->id),
            'photo'                      => $this->photo,
            'name'                       => $this->name,
            'description'                => $this->description,
            'created_at'                 => (new Carbon($this->created_at))->format('d/m/Y'),
            'shopify_id'                 => $this->shopify_id,
            'logo'                       => $this->logo,
            'url_page'                   => $this->url_page,
            'contact'                    => $this->contact,
            'contact_verified'           => $this->contact_verified,
            'support_phone'              => $this->support_phone,
            'support_phone_verified'     => $this->support_phone_verified,
            'invoice_description'        => $this->invoice_description,
            'installments_amount'        => $this->installments_amount,
            'installments_interest_free' => $this->installments_interest_free,
            'boleto'                     => $this->boleto,
            'credit_card'                => $this->credit_card,
            'boleto_due_days'            => $this->boleto_due_days,
            'boleto_redirect'            => $this->boleto_redirect,
            'card_redirect'              => $this->card_redirect,
            'analyzing_redirect'         => $this->analyzing_redirect,
            'cost_currency_type'         => $this->present()->getCurrencyCost($this->cost_currency_type),
            'status'                     => isset($this->domains[0]->name) ? 1 : 0,
            'checkout_type'              => $this->checkout_type,
            "terms_affiliates"           => $this->terms_affiliates,
            "cookie_duration"            => $this->cookie_duration,
            "automatic_affiliation"      => $this->automatic_affiliation,
            "url_affiliates"             => $this->url_affiliates,
            "percentage_affiliates"      => $this->percentage_affiliates,
            'user_name'                  => $this->users[0]->name,
        ];
    }
}
