<?php

namespace Modules\Core\Observers;

use Modules\Core\Entities\Sale;
use Redis;

class SaleObserver
{

    /**
     * Handle the faq "updating" event.
     *
     * @param Sale $sale
     * @return void
     */
    public function updating(Sale $sale)
    {

        if ($sale->getOriginal('has_valid_tracking') != $sale->has_valid_tracking) {

            Redis::connection('redis-statement')->set("sale:has:tracking:{$sale->id}", $sale->has_valid_tracking);
        }
    }
}
