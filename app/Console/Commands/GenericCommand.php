<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Transaction;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $transactions = Transaction::with('company')
        ->where('created_at', '>', '2021-09-15')
        ->where('release_date', '<=', Carbon::now()->format('Y-m-d'))
        ->where('status_enum', Transaction::STATUS_PAID)
        ->whereIn('gateway_id', [8])
        ->whereNotNull('company_id')
        ->where(function ($where) {
            $where->where('tracking_required', false)
                ->orWhereHas('sale', function ($query) {
                    $query->where(function ($q) {
                        $q->where('has_valid_tracking', true)
                            ->orWhereNull('delivery_id');
                    });
                });
        });

        if (!empty($saleId)) {
            $transactions->where('sale_id', $saleId);
        }

        foreach ($transactions->cursor() as $transaction) {
            $company = $transaction->company;
            $this->line('');
            $this->line('asaas_balance '. $company->asaas_balance??0);
            $this->line('transaction value '. intval($transaction->value));
            $this->line('somando '. $company->asaas_balance .'+'. $transaction->value);
            $this->line(intval($company->asaas_balance) + intval($transaction->value));
            
        }
    }
}
