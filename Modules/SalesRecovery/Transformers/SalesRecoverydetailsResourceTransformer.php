<?php

namespace Modules\SalesRecovery\Transformers;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesRecoverydetailsResourceTransformer extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        report(
            new Exception(
                json_encode([
                    "products" => $this["products"],
                ]),
            ),
        );

        $products = [];
        foreach ($this["products"] as $product) {
            $products[] = [
                "photo" => $product->photo,
                "name" => $product->name,
                "amount" => $product->amount,
            ];
        }

        $client = [
            "name" => $this["client"]->name ?? "",
            "telephone" => $this["client"]->telephone ?? "",
            "whatsapp_link" => $this["client"]->whatsapp_link ?? "",
            "email" => $this["client"]->email ?? "",
            "document" => $this["client"]->document ?? "",
            "error" => $this["client"]->error ?? "",
        ];

        if (strpos($client["email"], "invalido") !== false) {
            $client["email"] = "Email não informado";
        }

        $delivery = [
            "street" => $this["delivery"]->street ?? "",
            "zip_code" => $this["delivery"]->zip_code ?? "",
            "city" => $this["delivery"]->city ?? "",
            "state" => $this["delivery"]->state ?? "",
        ];

        $checkout = [
            "sale_id" => $this["checkout"]->sale_id,
            "date" => $this["checkout"]->date,
            "hours" => $this["checkout"]->hours,
            "total" => $this["checkout"]->total,
            "ip" => $this["checkout"]->ip,
            "is_mobile" => $this["checkout"]->is_mobile,
            "operational_system" => $this["checkout"]->operational_system,
            "browser" => $this["checkout"]->browser,
            "src" => $this["checkout"]->src,
            "utm_source" => $this["checkout"]->utm_source,
            "utm_medium" => $this["checkout"]->utm_medium,
            "utm_campaign" => $this["checkout"]->utm_campaign,
            "utm_term" => $this["checkout"]->utm_term,
            "utm_content" => $this["checkout"]->utm_content,
        ];

        return [
            "checkout" => $checkout,
            "client" => $client,
            "products" => $products,
            "delivery" => $delivery,
            "status" => $this["status"],
            "link" => $this["link"],
            "method" => "boletoCartao",
        ];
    }
}
