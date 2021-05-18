<?php

namespace Modules\ActiveCampaign\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivecampaignEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => Hashids::encode($this->id),
            'event_sale'  => $this->event_sale,
            'event_text'  => $this->event_text,
            'add_tags'    => json_decode($this->add_tags, true),
            'remove_tags' => json_decode($this->remove_tags, true),
            'remove_list' => json_decode($this->remove_list, true),
            'add_list'    => json_decode($this->add_list, true),
        ];
    }
}