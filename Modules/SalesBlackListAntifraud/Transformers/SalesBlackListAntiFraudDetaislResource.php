<?php

namespace Modules\SalesBlackListAntifraud\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SalesBlackListAntiFraudDetaislResource
 * @package Modules\SalesBlackListAntifraud\Transformers
 */
class SalesBlackListAntiFraudDetaislResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id'             => Hashids::connection('sale_id')->encode($this->id),
            'delivery_id'    => Hashids::encode($this->delivery_id),
            'checkout_id'    => Hashids::encode($this->checkout_id),
            'client_id'      => Hashids::encode($this->customer_id),
            //sale
            'payment_method' => $this->payment_method,
            'flag'           => $this->flag,
            'start_date'     => $this->start_date,
            'status'         => $this->status,

        ];
    }
}
