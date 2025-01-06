<?php

namespace Modules\Projects\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Affiliate;

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
class ProjectsResource extends JsonResource
{
    public function toArray($request): array
    {
        if (isset($this->affiliate_id)) {
            $affiliate = "";
            if ($this->affiliate_id > 0) {
                $affiliate = new Affiliate();
                $affiliate->id = $this->affiliate_id;
                $affiliate->created_at = $this->affiliate_created_at;
                $affiliate->percentage = $this->affiliate_percentage;
                $affiliate->status_enum = $this->affiliate_status;
            }
        } else {
            $affiliate = $this->affiliates[0] ?? "";
        }
        $affiliated = !empty($affiliate);

        $notazzConfig = json_decode($this->notazz_configs);
        $billterReleaseDays = $this->usersProjects->first()->company->bank_slip_release_money_days;

        $status = (isset($this->nuvemshop_id) ? ($this->status ? 1 : 0) : isset($this->domains[0]->name)) ? 1 : 0;

        return [
            "id" => hashids_encode($this->id),
            "photo" => $this->photo,
            "name" => $this->name,
            "description" => $this->description,
            "discount_recovery_status" => $this->discount_recovery_status,
            "discount_recovery_value" => $this->discount_recovery_value,
            "created_at" => (new Carbon($this->created_at))->format("d/m/Y"),
            "shopify_id" => $this->shopify_id,
            "woocommerce_id" => $this->woocommerce_id,
            "nuvemshop_id" => $this->nuvemshop_id,
            "url_page" => $this->url_page,
            "boleto_redirect" => $this->boleto_redirect,
            "billet_release_days" => $billterReleaseDays ?? "",
            "card_redirect" => $this->card_redirect,
            "pix_redirect" => $this->pix_redirect,
            "analyzing_redirect" => $this->analyzing_redirect,
            "cost_currency_type" => $this->present()->getCurrencyCost($notazzConfig->cost_currency_type ?? 1),
            "update_cost_shopify" => $notazzConfig->update_cost_shopify ?? 1,
            "status" => $status,
            "terms_affiliates" => $this->terms_affiliates,
            "cookie_duration" => $this->cookie_duration,
            "automatic_affiliation" => $this->automatic_affiliation,
            "url_affiliates" => route("affiliates.index", hashids_encode($this->id)),
            "percentage_affiliates" => $this->percentage_affiliates,
            "affiliated" => $affiliated,
            "affiliate_id" => hashids_encode($affiliate->id ?? ""),
            "affiliate_date" => !empty($affiliate->created_at)
                ? (new Carbon($affiliate->created_at))->format("d/m/Y")
                : "",
            "status_url_affiliates" => $this->status_url_affiliates,
            "commission_type_enum" => $this->commission_type_enum,
            "commission_affiliate" => $affiliate->percentage ?? "",
            "status_affiliate" => $affiliate->status_enum ?? "",
            "producer" => $this->producer ?? "",
            "reviews_config_icon_type" => $this->reviews_config_icon_type,
            "reviews_config_icon_color" => $this->reviews_config_icon_color,
            "chargeback_count" => $this->chargeback_count ?? 0,
            "open_tickets" => $this->open_tickets ?? 0,
            "without_tracking" => $this->without_tracking ?? 0,
            "approved_sales" => $this->approved_sales ?? 0,
            "approved_sales_value" => $this->approved_sales_value
                ? substr_replace(@$this->approved_sales_value, ".", strlen(@$this->approved_sales_value) - 2, 0)
                : 0,
            'created_by_checkout_integration' => !is_null($this->apiToken),
        ];
    }
}
