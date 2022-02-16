<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\Log;

class CheckHasValidTracking extends Command
{
    protected $signature = 'check:has-valid-tracking';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

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

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));
    }
}
