<?php

namespace Modules\SalesBlackListAntifraud\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SalesBlackListAntiFraudDetaislResource
 * @package Modules\SalesBlackListAntifraud\Transformers
 */
class SalesBlackListAntiFraudDetaislResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        //set flag
        if ((!$this->flag || empty($this->flag)) && ($this->payment_method == 1 || $this->payment_method == 3)) {
            $this->flag = 'generico';
        } else {
            if (!$this->flag || empty($this->flag)) {
                $this->flag = 'boleto';
            }
        }

        return [
            'id'             => Hashids::connection('sale_id')->encode($this->id),
            'delivery_id'    => Hashids::encode($this->delivery_id),
            'checkout_id'    => Hashids::encode($this->checkout_id),
            'customer_id'    => Hashids::encode($this->customer_id),
            //sale
            'payment_method' => $this->payment_method,
            'flag'           => $this->flag,
            'start_date'     => \Carbon\Carbon::parse($this->start_date)->format('d/m/Y H:i:s'),
            'status'         => $this->status,

        ];
    }
}
