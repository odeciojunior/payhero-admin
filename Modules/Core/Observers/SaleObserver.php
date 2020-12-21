<?php

namespace Modules\Core\Observers;

use Exception;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Sale;

class SaleObserver
{
    public function updating(Sale $sale)
    {
        try {
            if ($sale->getOriginal('has_valid_tracking') != $sale->has_valid_tracking) {
                Redis::connection('redis-statement')->set("sale:has:tracking:{$sale->id}", $sale->has_valid_tracking);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
