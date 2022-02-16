<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\Log;

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            Sale::with('transactions')
                ->where('gateway_id', 15)->chunk(
                    1000,
                    function ($sales) {
                        foreach ($sales as $sale) {
                            $this->info(' - ' . $sale->id . ' :: Database: ' . $sale->has_valid_tracking . ' | getValidTrackingForRedis: ' . $sale->getValidTrackingForRedis());
                            Redis::connection('redis-statement')->set(
                                "sale:has:tracking:{$sale->id}", $sale->getValidTrackingForRedis()
                            );
                        }
                    }
                );

            return 0;
        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
