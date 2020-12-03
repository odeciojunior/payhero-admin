<?php

namespace Modules\Pixels\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Entities\Pixel;
use Vinkla\Hashids\Facades\Hashids;

class PixelsResource extends JsonResource
{
    public function toArray($request)
    {
        $pixelPresenter = (new Pixel())->present();
        return [
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'code' => $this->code,
            'platform' => $this->platform,
            'platform_enum' => $pixelPresenter->getPlatformEnum($this->platform),
            'status' => $this->status,
            'affiliate_id' => Hashids::encode($this->affiliate_id),
            'status_translated' => Lang::get(
                'definitions.enum.pixel.status.' . $this->present()->getStatus($this->status)
            ),
        ];
    }
}
