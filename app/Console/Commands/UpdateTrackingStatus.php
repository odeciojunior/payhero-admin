<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\TrackingHistory;
use Modules\Core\Services\PerfectLogService;
use Vinkla\Hashids\Facades\Hashids;

class UpdateTrackingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateTrackingStatus';

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

            $trackingModel = new Tracking();
            $perfectLogService = new PerfectLogService();

            $trackings = $trackingModel->all();

            foreach ($trackings as $tracking){

                $this->line('Tracking: ' . $tracking->tracking_code);

                /*if($this->confirm('Do you wish to continue? (yes|no)[no]'))
                {
                    $this->info("Process terminated by user");
                    return;
                }*/

                $response =  $perfectLogService->track(Hashids::encode($tracking->id), $tracking->tracking_code);

                if(isset($response->tracking)){

                    $status = 1;

                    switch ($response->tracking->tracking_status) {
                        //case 'pending':
                        case 'preparation':
                            $status = $trackingModel->present()->getTrackingStatusEnum('posted');
                            break;
                        case 'sent':
                        case 'resend':
                            $status = $trackingModel->present()->getTrackingStatusEnum('dispatched');
                            break;
                        case 'delivered':
                            $status = $trackingModel->present()->getTrackingStatusEnum('delivered');
                            break;
                        case 'out_for_delivery':
                            $status = $trackingModel->present()->getTrackingStatusEnum('out_for_delivery');
                            break;
                        case 'canceled':
                        case 'erro_fiscal':
                        case 'returned':
                            $status = $trackingModel->present()->getTrackingStatusEnum('exception');
                            break;
                    }

                    $trackingStatusOld = $tracking->tracking_status_enum;
                    $tracking->tracking_status_enum = $status;
                    $tracking->save();

                    $trackingHistoryModel = new TrackingHistory();
                    $trackingHistoryModel->firstOrNew([
                        'tracking_id' => $tracking->id,
                        'tracking_status_enum' => $trackingStatusOld,
                    ]);

                    $this->line(json_encode($response->tracking, JSON_PRETTY_PRINT));
                }

            }
            $this->line(date('Y-m-d H:i:s') . ' Funcionou paizao!');
        }catch (Exception $e){
            $this->line(date('Y-m-d H:i:s') . ' Error: ' . $e->getMessage());
        }
    }
}
