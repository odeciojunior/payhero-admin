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
class AffiliateResource extends JsonResource
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
            "cellphone" => $this->user->cellphone ?? "",
            "company" => $this->company->fantasy_name ?? "",
            "status" => $this->status_enum,
            "percentage" => $this->percentage ? $this->percentage . "%" : "",
            "date" => Carbon::createFromFormat("Y-m-d H:i:s", $this->created_at)->format("d/m/Y H:i:s"),
            "status_translated" => Lang::get(
                "definitions.enum.status_affiliate." . $this->present()->getStatus($this->status_enum)
            ),
            "project_name" => $this->project->name,
            "project_photo" => $this->project->photo,
            "project_logo" => $this->project->checkoutConfig->checkout_logo,
            "suport_phone" => $this->suport_phone,
        ];
    }
}
