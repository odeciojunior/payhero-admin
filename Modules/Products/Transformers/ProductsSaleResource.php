<?php

namespace Modules\Products\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class ProductsResource
 * @package Modules\Products\Transformers
 */
class ProductsSaleResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => $this->id_code,
            'photo' => $this->photo,
            'name' => $this->name,
            'description' => !empty($this->description) ? Str::limit($this->description, 23) : '',
            'sale_status' => $this->sale_status ?? null,
            'amount' => $this->amount ?? null,
            'tracking_id' => $this->tracking_id ?? null,
            'tracking_code' => $this->tracking_code ?? null,
            'tracking_status_enum' => $this->tracking_status_enum ?? null,
            'tracking_created_at' => $this->tracking_created_at ?? null,
        ];
    }
}
