<?php

namespace Modules\ProjectReviewsConfig\Transformers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class PreviewReviewResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws Exception
     */
    public function toArray($request)
    {

        return [
            'id'         => Hashids::encode($this->id),
            'icon_type'  => $this->icon_type,
            'icon_color' => $this->icon_color
        ];
    }
}
