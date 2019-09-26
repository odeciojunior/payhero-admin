<?php

namespace Modules\Domains\Transformers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

/**
 * Class DomainResource
 * @property mixed name
 * @property mixed status
 * @property mixed id
 * @package Modules\Domains\Transformers
 */
class DomainResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => Hashids::encode($this->id),
            'domain'            => $this->name,
            'status'            => $this->status,
            'status_translated' => Lang::get('definitions.enum.status.' . $this->present()->getStatus($this->status)),
        ];
    }
}
