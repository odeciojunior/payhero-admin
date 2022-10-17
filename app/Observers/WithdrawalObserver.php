<?php

namespace App\Observers;

use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\CacheService;

class WithdrawalObserver
{
    /**
     * Handle the Withdrawal "created" event.
     *
     * @param  \App\Withdrawal  $withdrawal
     * @return void
     */
    public function created(Withdrawal $withdrawal)
    {
        CacheService::clearBalanceCache($withdrawal->company_id);
    }

    /**
     * Handle the Withdrawal "updated" event.
     *
     * @param  \App\Withdrawal  $withdrawal
     * @return void
     */
    public function updated(Withdrawal $withdrawal)
    {
        CacheService::clearBalanceCache($withdrawal->company_id);
    }
}
