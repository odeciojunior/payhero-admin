<?php

namespace Modules\Core\Services;

/**
 * Class SaleService
 * @package Modules\Core\Services
 */
class SaleService
{
    /**
     * @return float|int
     */
    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->plansSales as $planSale) {
            $subTotal += preg_replace("/[^0-9]/", "", $planSale->plan()->first()->price) * $planSale->amount;
        }

        return $subTotal;
    }
}
