<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTrackingJob;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Tracking;

class GenericCommand extends Command
{
    protected $signature = "generic";
    protected $description = "Command description";

    public function handle()
    {
        try {

            $trackings = Tracking::select("product_plan_sale_id", "tracking_code")
                ->where("system_status_enum", Tracking::SYSTEM_STATUS_POSTED_BEFORE_SALE)
                ->where(function($q) {
                    $q->whereDate("created_at", ">=", now()->subMonths(4));
                    $q->orWhereDate("updated_at", ">=", now()->subMonths(4));
                });


            $bar = $this->output->createProgressBar($trackings->count());
            $bar->start();

            foreach($trackings->cursor() as $key=>$tracking) {

                ProcessTrackingJob::dispatch($tracking);

                $bar->advance();
            }

            $bar->finish();

        } catch (Exception $e) {
            report($e->getMessage());
        }
    }

}
