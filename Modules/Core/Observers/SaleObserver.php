<?php

namespace Modules\Core\Observers;

use Modules\Core\Entities\Sale;

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

        // REDIS
    }
}
