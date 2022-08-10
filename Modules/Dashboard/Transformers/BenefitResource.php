<?php

namespace Modules\Dashboard\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BenefitResource
 * @package Modules\Dashboard\Transformers
 */
class BenefitResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => __("definitions.benefit." . $this->name),
            "level" => $this->level,
            "description" => $this->description,
            "enabled" => $this->enabled,
        ];
    }
}
