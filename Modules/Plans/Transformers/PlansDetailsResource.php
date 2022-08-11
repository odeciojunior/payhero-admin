<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PlansDetailsResource extends JsonResource
{
    public function toArray($request): array
    {
        $products = [];
        foreach ($this->productsPlans as $productsPlan) {
            $photo = "/build/global/img/produto.svg";
            if (!empty($productsPlan->product->photo)) {
                if (\foxutils()->remoteUrlExists($productsPlan->product->photo)) {
                    $photo = $productsPlan->product->photo;
                }
            }

            $limit_name = 24;

            $products[] = [
                "id" => hashids_encode($productsPlan->id),
                "product_id" => hashids_encode($productsPlan->product_id),
                "product_name_short_flag" =>
                    mb_strwidth($productsPlan->product->name, "UTF-8") <= $limit_name ? false : true,
                "product_name" => $productsPlan->product->name,
                "product_name_short" => Str::limit($productsPlan->product->name, $limit_name),
                "shopify_id" => $productsPlan->product->shopify_id,
                "variant_id" => $productsPlan->product->shopify_variant_id,
                "photo" => $photo,
                "amount" => $productsPlan->amount,
                "currency_type_enum" => $productsPlan->currency_type_enum,
                "product_cost" =>
                    ($productsPlan->currency_type_enum == 1 ? 'R$ ' : '$ ') .
                    number_format(($productsPlan->cost ?? 0) / 100, 2, ".", ","),
                "currency" => $productsPlan->present()->getCurrency($productsPlan->currency_type_enum),
                "custom_configs" => !empty($productsPlan->custom_config) ? $productsPlan->custom_config : [],
                "is_custom" => $productsPlan->is_custom > 0,
            ];
        }

        $limit_name = 24;
        $limit_description = 38;

        return [
            "id" => hashids_encode($this->id),
            "name" => $this->name,
            "name_short" => Str::limit($this->name, $limit_name),
            "name_short_flag" => mb_strwidth($this->name, "UTF-8") <= $limit_name ? false : true,
            "description" => $this->description,
            "description_short" => Str::limit($this->description, $limit_description),
            "description_short_flag" => mb_strwidth($this->description, "UTF-8") <= $limit_description ? false : true,
            "code" => isset($this->project->domains[0]->name)
                ? "https://checkout." . $this->project->domains[0]->name . "/" . $this->code
                : "Domínio não configurado",
            "price" => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, ",", "."),
            "status" => isset($this->project->domains[0]->name) ? 1 : 0,
            "status_translated" => isset($this->project->domains[0]->name) ? "Ativo" : "Desativado",
            "products" => $products,
        ];
    }
}
