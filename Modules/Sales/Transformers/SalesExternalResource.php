<?php

namespace Modules\Sales\Transformers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Core\Entities\Affiliate;

/**
 * Class SalesExternalResource
 * @package Modules\Sales\Transformers
 */
class SalesExternalResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $fee = preg_replace("/[^0-9]/", "", $this->details->taxaReal)/100;
        $fee += preg_replace("/[^0-9]/", "", $this->installment_tax_value ?? 0)/100;

        return [
            'id' => Hashids::connection('sale_id')->encode($this->id),
            'amount' => (float) number_format(preg_replace("/[^0-9]/", "", $this->details->total)/100, 2),
            'fee' => (float) number_format($fee, 2),
            'net_amount' => (float) number_format(preg_replace("/[^0-9]/", "", $this->details->comission)/100, 2),
            'payment_method' => $this->present()->getPaymentType(),
            'status' => $this->present()->getStatus(),
            'approved_at' => $this->end_date,
            'products' => $this->products ?? [],
        ];
    }
}
