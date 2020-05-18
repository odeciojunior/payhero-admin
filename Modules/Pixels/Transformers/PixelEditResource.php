<?php

namespace Modules\Pixels\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PixelEditResource
 * @package Modules\Pixels\Transformers
 */
class PixelEditResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id_code'         => $this->id_code,
            'name'            => $this->name,
            'platform'        => $this->platform,
            'code'            => $this->code,
            'status'          => $this->status,
            'checkout'        => $this->checkout,
            'purchase_boleto' => $this->purchase_boleto,
            'purchase_card'   => $this->purchase_card,
            'apply_on_plans'  => $this->apply_on_plans,
        ];
    }
}
