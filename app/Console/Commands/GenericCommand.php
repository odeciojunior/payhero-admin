<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        try {

            $tService = new TrackingService();

            $trackings = Tracking::with('productPlanSale')
                ->whereIn('tracking_code', [
                    'LB742801369SE',
                    'LB770911135SE',
                    'LB132612191HK',
                    'LZ486416437CN',
                    'LB162492183HK',
                    'LB162493100HK',
                    'LB162487824HK',
                    'LB162487838HK',
                    'SYAE003406761',
                    'LB163431557HK',
                    'LB163428972HK',
                    'LB163427889HK',
                    'LB163432328HK',
                    'LP00427410422172',
                    'LZ596867765CN',
                    'LB163434108HK',
                    'LB163434099HK',
                    'LB163429862HK',
                    'LB397462015SE',
                    'LB397697735SE',
                    'LB163993566HK',
                    'LB164568392HK',
                    'LB165130081HK',
                    'LB165214453HK',
                    'LZ608340514CN',
                    'LZ608340545CN',
                    'LZ608340151CN',
                    'LZ608340695CN',
                    'LZ608340426CN',
                    'LZ608340236CN',
                    'LZ608340032CN',
                    'LB165209688HK',
                    'LB165206749HK',
                    'LB165211315HK',
                    'LB091488435HK',
                    'LB091484898HK',
                    'LB091484005HK',
                    'LB091484915HK',
                    'LB091491125HK',
                    'LB166020652HK',
                    'LB166018906HK',
                ])
                ->orderByDesc('id')
                ->get();

            foreach ($trackings as $t) {
                $tService->createOrUpdateTracking($t->tracking_code, $t->productPlanSale);
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
