<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\TrackingmoreService;

class UpdateTrackingsIgnoredStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateTrackingsIgnoredStatus';

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
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function handle()
    {
        $trackingmoreService = new TrackingmoreService();
        $trackingsPresenter = (new Tracking())->present();

        $trackings = Tracking::where('tracking_status_enum', 6)
            ->get();

        foreach ($trackings as $tracking) {
            $apiTracking = $trackingmoreService->find($tracking->tracking_code);
            if(!empty($apiTracking)){
                $status = $trackingmoreService->parseStatus($apiTracking->status);
                $tracking->tracking_status_enum = $status;
            } else {
                $tracking->tracking_status_enum = 1;
            }
            $tracking->system_status_enum = $trackingsPresenter->getSystemStatusEnum('ignored');
            $tracking->save();
        }
    }
}
