<?php

namespace Modules\Trackings\Transformers;

use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingShowResource extends JsonResource
{
    public function toArray($request)
    {
        $linkBase = $this->productPlanSale->plan->project->domains->first()->name ?? '';

        return [
            'id' => Hashids::encode($this->id),
            'tracking_code' => $this->tracking_code,
            'tracking_status_enum' => $this->tracking_status_enum,
            'tracking_status' => $this->tracking_status_enum ? __('definitions.enum.tracking.tracking_status_enum.' . $this->present()->getTrackingStatusEnum($this->tracking_status_enum)) : 'Não informado',
            'created_at' => Carbon::parse($this->created_at)->format('d/m/Y'),
            'amount' => $this->amount,
            'product' => [
                'name' => $this->product->name,
                'description' => $this->product->description,
                'photo' => $this->product->photo,
            ],
            'delivery' => [
                'street' => $this->delivery->street,
                'number' => $this->delivery->number,
                'neighborhood' => $this->delivery->neighborhood,
                'zip_code' => $this->delivery->zip_code,
                'city' => $this->delivery->city,
                'state' => $this->delivery->state,
            ],
            'checkpoints' => $this->checkpoints ?? [],
            'link' => $linkBase ? 'https://tracking.' . $linkBase . '/' . $this->tracking_code : '',
        ];
    }
}
