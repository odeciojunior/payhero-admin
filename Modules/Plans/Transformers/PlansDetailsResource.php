<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class PlansDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        $configProduct = $this->config_personalization_product;
        $products = [];
        foreach ($this->productsPlans as $productsPlan) {
            $products[] = [
                'id'           => $productsPlan->id,
                'product_id'   => $productsPlan->product_id,
                'product_name' => $productsPlan->product->name,
                'shopify_id'   => $productsPlan->product->shopify_id,
                'variant_id'   => $productsPlan->product->shopify_variant_id,
                'photo'         =>  $productsPlan->product->photo,
                'amount'       => $productsPlan->amount,
                'product_cost' => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $productsPlan->cost)) / 100, 2, '.', ','),
                'currency'     => $productsPlan->present()->getCurrency($productsPlan->currency_type_enum),
                'custom_configs'=> !empty($productsPlan->custom_config) ? $productsPlan->custom_config : []
            ];
        }

        return [
            'id'                => Hashids::encode($this->id),
            'name'              => $this->name,
            'description'       => $this->description,
            'code'              => isset($this->project->domains[0]->name) ? 'https://checkout.' . $this->project->domains[0]->name . '/' . $this->code : 'Domínio não configurado',
            'price'             => 'R$' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, '.', ','),
            'status'            => isset($this->project->domains[0]->name) ? 1 : 0,
            'status_translated' => isset($this->project->domains[0]->name) ? 'Ativo' : 'Desativado',
            'products'          => $products,
        ];
    }
}
