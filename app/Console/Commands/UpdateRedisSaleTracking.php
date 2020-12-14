<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Redis;

class UpdateRedisSaleTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:update-sale-tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $sales = Sale::where('gateway_id', 15)->orderBy('id', 'desc')->get();
        foreach ($sales as $sale) {

            $this->info(' - ' . $sale->id . ' :: ' . $sale->has_valid_tracking);
            Redis::connection('redis-statement')->set("sale:has:tracking:{$sale->id}", $sale->has_valid_tracking);
        }

        return 0;
    }
}
