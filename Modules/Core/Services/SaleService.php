<?php

namespace Modules\Core\Services;

use Modules\Core\Entities\Sale;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SaleService
 * @package Modules\Core\Services
 */
class SaleService
{
    /**
     * @param Sale $sale
     * @return float|int
     */
    public function getSubTotal(Sale $sale)
    {
        $subTotal = 0;
        foreach ($sale->plansSales as $planSale) {
            $subTotal += preg_replace("/[^0-9]/", "", $planSale->plan()->first()->price) * $planSale->amount;
        }

        return $subTotal;
    }

    /**
     * @param Sale $sale
     * @return array
     */
    public function getPagarmeItensList(Sale $sale)
    {
        $itens = [];

        foreach ($sale->plansSales as $key => $planSale) {
            $itens[] = [
                'id'         => '#' . Hashids::encode($planSale->plan->id),
                'title'      => $planSale->plan->name,
                'unit_price' => str_replace('.', '', $planSale->plan->price),
                'quantity'   => $planSale->amount,
                'tangible'   => true,
            ];
        }

        return $itens;
    }
}
