<?php

namespace Modules\Mobile\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class StatementsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'gateways_balances'        => $this['gateways_balances'],
            'total_gateways_available' => $this['total_gateways_available'],
            'total_balance'            => $this['total_balance']
        ];

        return $data;
    }
}
