<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Tracking;
use Modules\Core\Events\CheckSaleHasValidTrackingEvent;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $this->info("APROVANDO RASTREIOS");
        $trackingsQuery = Tracking::select(DB::raw('trackings.*'))
                            ->join('sales as s', 'trackings.sale_id', '=', 's.id')
                            ->where('s.project_id', 5496);

        $trackings = $trackingsQuery->get();

        $trackingsQuery->update([
            'system_status_enum' => Tracking::SYSTEM_STATUS_CHECKED_MANUALLY
        ]);

        $bar = $this->getOutput()->createProgressBar($trackings->count());
        $bar->start();
        foreach ($trackings as $tracking) {
            event(new CheckSaleHasValidTrackingEvent($tracking->sale_id));
            $bar->advance();
        }
        $bar->finish();
    }

}
