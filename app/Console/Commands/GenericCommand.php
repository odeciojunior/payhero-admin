<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\ReportanaTrackingEvent;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $bar = $this->getOutput()->createProgressBar();

        $this->info('ENVIANDO ATUALIZAÇÕES DE RASTREIO');

        $salesForTrackingUpdate = Sale::select('id')
            ->where('owner_id', 557)
            ->where('start_date', '<=', now()->subDays(30))
            ->where('status', Sale::STATUS_APPROVED)
            ->where('payment_method', Sale::CREDIT_CARD_PAYMENT)
            ->where('has_valid_tracking', 1)
            ->get();

        $bar->start($salesForTrackingUpdate->count());
        foreach ($salesForTrackingUpdate as $sale) {
            event(new ReportanaTrackingEvent($sale->id));
            $bar->advance();
        }
        $bar->finish();

        $this->info('ENVIANDO VENDAS DE EXPIRADAS');

        $salesExpired = Sale::select('id')
            ->where('owner_id', 557)
            ->where('start_date', '<=', now()->subDays(30))
            ->where('status', Sale::STATUS_CANCELED)
            ->whereIn('payment_method', [Sale::PIX_PAYMENT, Sale::PAYMENT_TYPE_BANK_SLIP])
            ->get();

        $bar->start($salesExpired->count());
        foreach ($salesExpired as $sale) {
            event(new ReportanaTrackingEvent($sale->id, false));
            $bar->advance();
        }
        $bar->finish();
    }
}
