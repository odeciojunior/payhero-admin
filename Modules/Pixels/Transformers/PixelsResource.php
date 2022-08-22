<?php

namespace Modules\Pixels\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class PixelsResource extends JsonResource
{
    public function toArray($request): array
    {
        $platform = Lang::get("definitions.enum.pixel.platform." . $this->platform);
        $statusTranslated = Lang::get("definitions.enum.pixel.status." . $this->present()->getStatus($this->status));

        return [
            "id" => hashids_encode($this->id),
            "name" => $this->name,
            "name_short" => Str::limit($this->name, 24),
            "code" => $this->code,
            "platform" => $this->platform,
            "platform_enum" => $platform,
            "status" => $this->status,
            "affiliate_id" => hashids_encode($this->affiliate_id),
            "status_translated" => $statusTranslated,
            "is_api" => $this->is_api,
            "facebook_token" => $this->facebook_token,
            "value_percentage_purchase_boleto" => $this->value_percentage_purchase_boleto,
            "value_percentage_purchase_pix" => $this->value_percentage_purchase_pix,
        ];
    }
}
