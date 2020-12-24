<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Sale;

class TestSaleObserverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TestSaleObserverCommand';

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

        $sale = Sale::find(896608);
        //Redis::connection('redis-statement')->set("sale:has:tracking:{$sale->id}", $sale->has_valid_tracking);

        $hasValidTracking = (boolean)Redis::connection('redis-statement')->get("sale:has:tracking:{$sale->id}");

        dd($hasValidTracking);
        /*$sale = Sale::find(880868);
        $sale->has_valid_tracking = 0;
        $sale->save();
        return 0;*/
    }
}
