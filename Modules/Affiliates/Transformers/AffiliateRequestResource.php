<?php

namespace Modules\Affiliates\Transformers;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Lang;

/**
 * @property mixed id
 * @property mixed name
 */
class AffiliateRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => Hashids::encode($this->id),
            "name" => $this->user->name ?? "",
            "email" => $this->user->email ?? "",
            "status" => $this->status,
            "date" => !is_null($this->created_at)
                ? Carbon::createFromFormat("Y-m-d H:i:s", $this->created_at)->format("d/m/Y H:i:s")
                : "",
            "status_translated" => Lang::get("definitions.enum.status." . $this->present()->getStatus($this->status)),
            "project_name" => $this->project->name,
        ];
    }
}
