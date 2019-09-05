<?php

namespace Modules\SalesRecovery\Transformers;

use Illuminate\Http\Resources\Json\Resource;

class SalesRecoveryCartAbandonedDetailsResourceTransformer extends Resource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'checkout' => $this['checkout'],
            'client'   => $this['client'],
            'products' => $this['products'],
            'delivery' => $this['delivery'],
            'status'   => $this['status'],
            'link'     => $this['link'],
            'method'   => 'cartAbandoned',

        ];
    }
}
