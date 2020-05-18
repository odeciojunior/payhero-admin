<?php

namespace Modules\Pixels\Transformers;

use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class PixelsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => Hashids::encode($this->id),
            'name'              => $this->name,
            'code'              => $this->code,
            'platform'          => $this->platform,
            'status'            => $this->status,
            'affiliate_id'      => Hashids::encode($this->affiliate_id),
            'status_translated' => Lang::get('definitions.enum.pixel.status.' . $this->present()->getStatus($this->status)),
        ];
    }
}
