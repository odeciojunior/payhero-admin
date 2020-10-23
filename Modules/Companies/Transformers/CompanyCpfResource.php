<?php

namespace Modules\Companies\Transformers;

use Illuminate\Http\Request;
use Modules\Core\Entities\UserProject;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Services\CompanyService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CompanyCpfResource
 * @package Modules\Companies\Transformers
 */
class CompanyCpfResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  Request
     * @return array
     */
    public function toArray($request)
    {
        $presenter = $this->resource->present();
        $project = UserProject::where('company_id', $this->resource->id)->first();

        return [
            'id_code' => Hashids::encode($this->resource->id),
            'user_code' => Hashids::encode($this->resource->user_id),
            'bank' => $this->resource->bank ?? '',
            'agency' => $this->resource->agency ?? '',
            'agency_digit' => $this->resource->agency_digit ?? '',
            'account' => $this->resource->account ?? '',
            'account_digit' => $this->resource->account_digit ?? '',
            'document_status' => $presenter->getBankDocumentStatus(),
            'country' => $this->country ?? '',
            'type' => $this->company_type,
            'account_type' => $this->account_type ?? '',
            'active_flag' => $this->active_flag,
            'has_project' => !empty($project),
            'capture_transaction_enabled' => $this->capture_transaction_enabled,
            'boleto_release_money' => $this->boleto_release_money_days,
            'credit_card_tax' => $this->credit_card_tax,
            'boleto_tax' => $this->boleto_tax,
            'credit_card_release_money' => $this->credit_card_release_money_days,
            'gateway_tax' => $this->gateway_tax,
            'installment_tax' => $this->installment_tax
        ];
    }
}


