<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;
use Illuminate\Http\Resources\Json\JsonResource;

class PlansSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $photo = '/modules/global/img/produto.svg';
        if (!empty($this->productsPlans[0]->product->photo)) {
            if (\foxutils()->remoteUrlExists($this->productsPlans[0]->product->photo)) {
                $photo = $this->productsPlans[0]->product->photo;
            }
        }

        return [
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'name_short' => Str::limit($this->name, 14),
            'description' => $this->description,
            'custo' => 'R$' . number_format(intval(preg_replace("/[^0-9]/", "", $this->productsPlans[0]->cost)) / 100, 2, ',', '.'),
            'photo' => $photo
        ];
    }
}
