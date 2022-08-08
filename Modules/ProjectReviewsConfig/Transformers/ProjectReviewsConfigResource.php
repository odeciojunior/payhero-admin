<?php

namespace Modules\ProjectReviewsConfig\Transformers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ProjectReviewsConfigResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "reviews_config_icon_type" => $this->reviews_config_icon_type,
            "reviews_config_icon_color" => $this->reviews_config_icon_color,
        ];
    }
}
