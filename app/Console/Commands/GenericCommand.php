<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingmoreService;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic {user?}';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $trackingmoreService = new TrackingmoreService();

        $trackings = Tracking::whereDate('created_at', '>=', '2020-08-07 00:00:00')
        ->select('tracking_code')
            ->pluck('tracking_code');

        $total = $trackings->count();

        foreach ($trackings as $key => $tracking) {
            $i = $key+1;
            $this->line("Enviando tracking {$i} de {$total}");
            $trackingmoreService->createTracking($tracking);
        }

    }
}


