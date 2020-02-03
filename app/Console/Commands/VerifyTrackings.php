<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingService;

class VerifyTrackings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:verifyTrackings';

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
     * @return mixed
     */
    public function handle()
    {
        try{

            $trackingService = new TrackingService();

            $trackings = Tracking::where('tracking_status_enum', 1)
                ->orderByDesc('id')
                ->skip(0)
                ->take(1000)
                ->get();

            $count = $trackings->count();

            foreach ($trackings as $key => $tracking){

                $this->line(($key+1) . ' de ' . $count . '. Enviando tracking: ' . $tracking->tracking_code);

                $result = $trackingService->findTrackingApi($tracking);

                if(!empty($result->error)){
                    $this->line('Tem não zé');
                }else{
                    $this->line('Foi!');
                }
            }

        } catch (Exception $e) {
            $this->line($e->getMessage());
        }
    }
}
