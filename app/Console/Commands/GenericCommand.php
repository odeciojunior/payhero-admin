<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Modules\Core\Entities\Sale;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $sales = Sale::with('transactions')
            ->whereHas('transactions', function ($query) {
            $query->where('tracking_required', false);
        })->get();

        foreach ($sales as $sale) {
            try {
                Redis::connection('redis-statement')->set(
                    "sale:has:tracking:{$sale->id}", $sale->getValidTrackingForRedis()
                );
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}



