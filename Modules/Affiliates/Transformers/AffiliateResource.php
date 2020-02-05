<?php

namespace Modules\Affiliates\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;

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
            'id'                => Hashids::encode($this->id),
            'name'              => $this->user->name ?? null,
            'email'             => $this->user->email ?? null,
            'company'           => $this->company->fantasy_name ?? null,
            'status'            => $this->status_enum,
            'percentage'        => $this->percentage ? $this->percentage . '%' : '',
            'date'              => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d/m/Y H:i:s'),
            'status_translated' => Lang::get('definitions.enum.status.' . $this->present()
                                                                               ->getStatus($this->status_enum)),
        ];
    }
}
