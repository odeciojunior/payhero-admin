<?php

namespace Modules\Affiliates\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;

/**
 * @property mixed id
 * @property mixed name
 */
class AffiliateResource extends Resource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => Hashids::encode($this->id),
            'name'       => $this->user->name ?? null,
            'status'     => $this->status_enum,
            'percentage' => $this->percentage,
            'date'       => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d/m/Y H:i:s'),
        ];
    }
}
