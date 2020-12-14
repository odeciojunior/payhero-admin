<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Redis;

class UpdateRedisSaleTracking extends Command
{
    protected $signature = 'redis:update-sale-tracking';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $sales = Sale::where('gateway_id', 15)->chunk(
            500,
            function ($sales) {
                foreach ($sales as $sale) {
                    $this->info(' - ' . $sale->id . ' :: ' . $sale->has_valid_tracking);
                    Redis::connection('redis-statement')->set(
                        "sale:has:tracking:{$sale->id}",
                        $sale->has_valid_tracking
                    );
                }
            }
        );

        return 0;
    }
}
