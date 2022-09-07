<?php

namespace Modules\Api\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class SalesApiResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->sale->api_flag) {
            return [
                "transaction_id" => Hashids::connection("sale_id")->encode($this->id),
                "method" => $this->sale->present()->getPaymentType($this->sale->payment_method),
                "status" => $this->sale->present()->getStatus(),
                "total_price" => foxutils()->formatMoney($this->sale->total_paid_value),
                "shipping_price" => $this->sale->present()->getShipmentValue(),
                "paid_value" => $this->sale->present()->getTotalPaidValue(),
                "customer" => [
                    "name" => $this->sale->customer->name,
                    "document" => $this->sale->customer->document,
                    "email" => $this->sale->customer->email,
                    "phone" => $this->sale->customer->telephone,
                ],
                "products" => $this->sale->present()->getProductsApiData(),
            ];
        }

        return [
            "transaction_id" => Hashids::connection("sale_id")->encode($this->id),
            "store_name" => $this->sale->project->name,
            "method" => $this->sale->present()->getPaymentType($this->sale->payment_method),
            "total_price" => foxutils()->formatMoney($this->sale->total_paid_value),
            "status" => $this->sale->present()->getStatus(),
            "created_at" => $this->sale->created_at->format("d/m/Y H:i:s"),
            "customer" => [
                "name" => $this->sale->customer->name,
                "document" => $this->sale->customer->document,
                "email" => $this->sale->customer->email,
                "phone" => $this->sale->customer->telephone,
            ],
            "address" => [
                "street" => !empty($this->sale->delivery) ? $this->sale->delivery->street : null,
                "number" => !empty($this->sale->delivery) ? $this->sale->delivery->number : null,
                "district" => !empty($this->sale->delivery) ? $this->sale->delivery->district : null,
                "zip_code" => !empty($this->sale->delivery) ? $this->sale->delivery->zip_code : null,
                "city" => !empty($this->sale->delivery) ? $this->sale->delivery->city : null,
                "state" => !empty($this->sale->delivery) ? $this->sale->delivery->state : null,
                "country" => !empty($this->sale->delivery) ? $this->sale->delivery->country : null
            ],
            "plans" => [
                "id" => !empty($this->sale->plansSales[0]) ? Hashids::encode($this->sale->plansSales[0]->plan->id) : null,
                "name" => !empty($this->sale->plansSales[0]) ? $this->sale->plansSales[0]->plan->name : null,
                "description" => !empty($this->sale->plansSales[0]) ? $this->sale->plansSales[0]->plan->description : null,
                "amount" => !empty($this->sale->plansSales[0]) ? $this->sale->plansSales[0]->amount : null,
                "value" => !empty($this->sale->plansSales[0]) ? foxutils()->formatMoney($this->sale->plansSales[0]->plan_value) : null,
                "created_at" => !empty($this->sale->plansSales[0]) ? $this->sale->plansSales[0]->plan->created_at->format("d/m/Y H:i:s") : null,
                "products" => !empty($this->sale->plansSales[0]) ? $this->sale->present()->getProductsData() : []
            ]
        ];
    }
}
