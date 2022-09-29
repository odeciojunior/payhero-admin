<?php

namespace App\Observers;

use Modules\Core\Entities\Transfer;
use Modules\Core\Services\CacheService;

class TransferObserver
{
    public function created(Transfer $transfer)
    {
        CacheService::clearBalanceCache($transfer->company_id);
    }

    public function updated(Transfer $transfer)
    {
        CacheService::clearBalanceCache($transfer->company_id);
    }

    public function deleted(Transfer $transfer)
    {
        CacheService::clearBalanceCache($transfer->company_id);
    }

    public function forceDeleted(Transfer $transfer)
    {
        CacheService::clearBalanceCache($transfer->company_id);
    }
}
