<?php

namespace App\Observers;

use Modules\Core\Entities\Transaction;
use Modules\Core\Services\CacheService;

class TransactionObserver
{
    public function created(Transaction $transaction)
    {
        CacheService::clearBalanceCache($transaction->company_id);
    }

    public function updated(Transaction $transaction)
    {
        CacheService::clearBalanceCache($transaction->company_id);
    }

    public function deleted(Transaction $transaction)
    {
        CacheService::clearBalanceCache($transaction->company_id);
    }

    public function forceDeleted(Transaction $transaction)
    {
        CacheService::clearBalanceCache($transaction->company_id);
    }
}
