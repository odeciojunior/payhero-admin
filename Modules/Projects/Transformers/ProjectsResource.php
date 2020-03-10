<?php

namespace Modules\Projects\Transformers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Modules\Core\Entities\Affiliate;
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
        // $affiliate  = Affiliate::where('user_id', auth()->user()->account_owner_id)
        //                        ->where('project_id', $this->id)
        //                        ->first();
        $affiliate  = $this->affiliates[0] ?? '';
        $affiliated = !empty($affiliate) ? true : false;

        return [
            'id'                             => Hashids::encode($this->id),
            'photo'                          => $this->photo,
            'name'                           => $this->name,
            'description'                    => $this->description,
            'discount_recovery_status'       => $this->discount_recovery_status,
            'discount_recovery_value'        => $this->discount_recovery_value,
            'created_at'                     => (new Carbon($this->created_at))->format('d/m/Y'),
            'shopify_id'                     => $this->shopify_id,
            'logo'                           => $this->logo,
            'url_page'                       => $this->url_page,
            'contact'                        => $this->contact ?? '',
            'contact_verified'               => $this->contact_verified,
            'support_phone'                  => $this->support_phone ?? '',
            'support_phone_verified'         => $this->support_phone_verified,
            'invoice_description'            => $this->invoice_description,
            'installments_amount'            => $this->installments_amount,
            'installments_interest_free'     => $this->installments_interest_free,
            'boleto'                         => $this->boleto,
            'credit_card'                    => $this->credit_card,
            'boleto_due_days'                => $this->boleto_due_days,
            'boleto_redirect'                => $this->boleto_redirect,
            'card_redirect'                  => $this->card_redirect,
            'analyzing_redirect'             => $this->analyzing_redirect,
            'cost_currency_type'             => $this->present()->getCurrencyCost($this->cost_currency_type),
            'status'                         => isset($this->domains[0]->name) ? 1 : 0,
            'checkout_type'                  => $this->checkout_type,
            "terms_affiliates"               => $this->terms_affiliates,
            "cookie_duration"                => $this->cookie_duration,
            "automatic_affiliation"          => $this->automatic_affiliation,
            "url_affiliates"                 => route('index', Hashids::encode($this->id)),
            "percentage_affiliates"          => $this->percentage_affiliates,
            'affiliated'                     => $affiliated,
            'affiliate_id'                   => Hashids::encode($affiliate->id ?? ''),
            'affiliate_date'                 => (!empty($affiliate->created_at)) ? (new Carbon($affiliate->created_at))->format('d/m/Y') : '',
            "status_url_affiliates"          => $this->status_url_affiliates,
            "commission_type_enum"           => $this->commission_type_enum,
            "commission_affiliate"           => $affiliate->percentage ?? '',
            "status_affiliate"               => $affiliate->status_enum ?? '',
            "producer"                       => $this->producer ?? '',
            "release_money_days"             => $this->release_money_days ?? '',
            "credit_card_release_money_days" => $this->credit_card_release_money_days ?? '',
            "debit_card_release_money_days"  => $this->debit_card_release_money_days ?? '',
            "boleto_release_money_days"      => $this->boleto_release_money_days ?? '',
            'whatsapp_button'                => $this->whatsapp_button,
        ];
    }
}
