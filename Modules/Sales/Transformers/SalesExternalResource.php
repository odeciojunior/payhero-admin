<?php

namespace Modules\Sales\Transformers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\Resource;
use Modules\Core\Entities\Affiliate;

/**
 * Class SalesExternalResource
 * @package Modules\Sales\Transformers
 */
class SalesExternalResource extends Resource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => Hashids::connection('sale_id')->encode($this->id),
            'token' => Hashids::encode($this->checkout_id),
            'amount' => (float) number_format(preg_replace("/[^0-9]/", "", $this->details->total)/100, 2),
            'net_amount' => (float) number_format(preg_replace("/[^0-9]/", "", $this->details->comission)/100, 2),
            'payment_method' => $this->present()->getPaymentType(),
            'status' => $this->present()->getStatus(),
            'fee' => (float) number_format(preg_replace("/[^0-9]/", "", $this->details->taxaReal)/100, 2),
            'approved_at' => $this->end_date,
        ];
    }
}
