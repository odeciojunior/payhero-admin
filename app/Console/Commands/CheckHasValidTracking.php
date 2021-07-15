<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;

class CheckHasValidTracking extends Command
{
    protected $signature = 'check:has-valid-tracking';

    protected $description = 'Command description';

    public function handle()
    {
        try {
            $query = Sale::where('status', 1)
                ->where('has_valid_tracking', 0)
                ->whereHas('trackings', function ($tracking) {
                    $tracking->whereIn('system_status_enum', collect([1, 7]));
                })
                ->withCount([
                    'productsPlansSale',
                    'trackings' => function ($t) {
                        $t->whereIn('system_status_enum', collect([1, 7]));
                    }
                ]);

            $query->chunk(1000, function ($sales) {
                foreach ($sales as $sale) {
                    if ($sale->trackings_count == $sale->products_plans_sale_count) {
                        $sale->has_valid_tracking = true;
                        $sale->save();
                    }
                }
            });
        } catch (Exception $e) {
            report($e);
        }
    }
}
