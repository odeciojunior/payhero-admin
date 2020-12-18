<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        try {
            $trackingCodes = collect(DB::select(DB::raw("select tracking_code
                                                        from trackings
                                                        group by tracking_code
                                                        having count(*) > 1")))
                ->pluck('tracking_code')
                ->toArray();

            $trackingService = new TrackingService();

            $query = Tracking::with('productPlanSale')
                ->whereIn('tracking_code', $trackingCodes);

            $total = $query->count();
            $count = 0;

            $query->chunk(1000, function ($trackings) use (&$count, $total, $trackingService) {
                foreach ($trackings as $t){
                    $count++;
                    $this->line("Checking tracking {$count} de {$total}: $t->tracking_code");
                    $trackingService->createOrUpdateTracking($t->tracking_code, $t->productPlanSale);
                }
            });

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
