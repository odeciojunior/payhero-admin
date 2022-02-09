<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $this->info("VERIFICANDO RASTREIOS");

            $trackings = Tracking::select(DB::raw('trackings.*'))
                ->join('sales as s', 'trackings.sale_id', '=', 's.id')
                ->where('s.owner_id', 6191)
                ->get();

            $bar = $this->getOutput()->createProgressBar($trackings->count());
            $bar->start();
            foreach ($trackings as $tracking) {
                event(new CheckSaleHasValidTrackingEvent($tracking->sale_id));
                $bar->advance();
            }
            $bar->finish();

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
