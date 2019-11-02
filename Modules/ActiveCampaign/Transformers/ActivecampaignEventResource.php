<?php

namespace Modules\ActiveCampaign\Transformers;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;

class ActivecampaignEventResource extends Resource
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
            'product_id'  => Hashids::encode($this->product_id),
            'plan_id'     => Hashids::encode($this->plan_id),
            'plan'        => $this->plan->name ?? null,
            'product'     => $this->product->name,
            'event_sale'  => $this->event_sale,
            'add_tags'    => json_decode($this->add_tags, true),
            'remove_tags' => json_decode($this->remove_tags, true),
            'remove_list' => json_decode($this->remove_list, true),
            'add_list'    => json_decode($this->add_list, true),
        ];
    }
}