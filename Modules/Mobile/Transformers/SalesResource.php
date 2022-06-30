<?php

namespace Modules\Mobile\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Services\FoxUtils;

class SalesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $sale = $this->sale;

        if (!$sale->api_flag) {
            $product = (count($sale->getRelation('plansSales')) > 1) ? 'Carrinho' : (!empty($sale->plansSales->first()->plan->name) ? $sale->plansSales->first()->plan->name : '');
        }
        else {
            $product = 'Checkout api';
        }

        if (is_numeric($sale->payment_method)) {
            switch ($sale->payment_method) {
                case 1:
                    $paymentMethod = 'Cartão de Crédito';
                    break;
                case 2:
                    $paymentMethod = 'Boleto';
                    break;
                case 3:
                    $paymentMethod = 'Cartão de Débito';
                    break;
                case 4:
                    $paymentMethod = 'Pix';
                    break;
                default:
                    $paymentMethod = null;
            }
        }
        else {
            $paymentMethod = null;
        }

        $data = [
            'id'             => hashids_encode($sale->id, 'sale_id'),
            'product'        => $product,
            'total_paid'     => number_format(intval($this->value) / 100, 2, ',', '.'),
            'status'         => Lang::get('definitions.enum.sale.status.' . $sale->present()->getStatus($sale->status)),
            'payment_method' => $paymentMethod,
            'payment_time'   => FoxUtils::calcTime(Carbon::create($sale->start_date))
        ];

        return $data;
    }
}
