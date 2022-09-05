<?php

namespace Modules\Api\Transformers\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class SalesApiResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->api_flag) {
            return [
                "transaction_id" => Hashids::connection("sale_id")->encode($this->id),
                "method" => $this->present()->getPaymentType($this->payment_method),
                "status" => $this->present()->getStatus(),
                "total_price" => foxutils()->formatMoney($this->total_paid_value),
                "shipping_price" => $this->present()->getShipmentValue(),
                "paid_value" => $this->present()->getTotalPaidValue(),
                "customer" => [
                    "name" => $this->customer->name,
                    "document" => $this->customer->document,
                    "email" => $this->customer->email,
                    "phone" => $this->customer->telephone,
                ],
                "products" => $this->present()->getProductsApiData(),
            ];
        }

        return [
            "transaction_id" => Hashids::connection("sale_id")->encode($this->id),
            "store_name" => $this->project->name,
            "method" => $this->present()->getPaymentType($this->payment_method),
            "total_price" => foxutils()->formatMoney($this->total_paid_value),
            "status" => $this->present()->getStatus(),
            "created_at" => $this->created_at->format("d/m/Y H:i:s"),
            "customer" => [
                "name" => $this->customer->name,
                "document" => $this->customer->document,
                "email" => $this->customer->email,
                "phone" => $this->customer->telephone,
            ],
            "address" => [
                "street" => !empty($this->delivery) ? $this->delivery->street : null,
                "number" => !empty($this->delivery) ? $this->delivery->number : null,
                "district" => !empty($this->delivery) ? $this->delivery->district : null,
                "zip_code" => !empty($this->delivery) ? $this->delivery->zip_code : null,
                "city" => !empty($this->delivery) ? $this->delivery->city : null,
                "state" => !empty($this->delivery) ? $this->delivery->state : null,
                "country" => !empty($this->delivery) ? $this->delivery->country : null
            ],
            "plans" => [
                "id" => $this->plansSales[0]->plan->id,
                "name" => $this->plansSales[0]->plan->name,
                "description" => $this->plansSales[0]->plan->description,
                "amount" => $this->plansSales[0]->amount,
                "value" => foxutils()->formatMoney($this->plansSales[0]->plan_value),
                "created_at" => $this->plansSales[0]->plan->created_at->format("d/m/Y H:i:s"),
                "products" => $this->present()->getProductsData()
            ]
        ];
    }
}
