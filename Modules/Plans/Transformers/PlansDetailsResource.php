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
            $photo = '/modules/global/img/produto.svg';
            if (!empty($productsPlan->product->photo)) {
                if (\foxutils()->remoteUrlExists($productsPlan->product->photo)) {
                    $photo = $productsPlan->product->photo;
                }
            }
            $products[] = [
                'id' => $productsPlan->id,
                'product_id' => $productsPlan->product_id,
                'product_name' => $productsPlan->product->name,
                'product_name_short' => Str::limit($productsPlan->product->name, 24),
                'shopify_id' => $productsPlan->product->shopify_id,
                'variant_id' => $productsPlan->product->shopify_variant_id,
                'photo' => $photo,
                'amount' => $productsPlan->amount,
                'product_cost' => 'R$ ' . number_format(intval(preg_replace("/[^0-9]/", "", $productsPlan->cost)) / 100, 2, '.', ','),
                'currency' => $productsPlan->present()->getCurrency($productsPlan->currency_type_enum),
                'custom_configs' => !empty($productsPlan->custom_config) ? $productsPlan->custom_config : [],
                'is_custom' => $productsPlan->is_custom > 0
            ];
        }

        return [
            'id' => hashids_encode($this->id),
            'name' => $this->name,
            'description' => $this->description,
            'code' => isset($this->project->domains[0]->name) ? 'https://checkout.' . $this->project->domains[0]->name . '/' . $this->code : 'Domínio não configurado',
            'price' => 'R$' . number_format(intval(preg_replace("/[^0-9]/", "", $this->price)) / 100, 2, '.', ','),
            'status' => isset($this->project->domains[0]->name) ? 1 : 0,
            'status_translated' => isset($this->project->domains[0]->name) ? 'Ativo' : 'Desativado',
            'products' => $products,
        ];
    }
}
