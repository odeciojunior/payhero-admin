<?php

namespace Modules\Finances\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Core\Entities\Company;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class FinancesResource
 * @package Modules\Finances\Transformers
 */
class FinancesResource extends Resource
{
    /**
     * The resource instance.
     * @var Company
     */
    public $resource;

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id_code'      => Hashids::encode($this->resource->id),
            'fantasy_name' => $this->resource->fantasy_name ?? '',
        ];
    }
}
