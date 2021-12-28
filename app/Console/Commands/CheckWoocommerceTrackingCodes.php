<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Vinkla\Hashids\Facades\Hashids;

class CheckWoocommerceTrackingCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:check-tracking-codes';

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
        $sales = Sale::select('project_id')->whereNotNull('woocommerce_order')->where('has_valid_tracking', false)
        ->where('status', Sale::STATUS_APPROVED)
        ->groupBy('project_id')
        ->get();

        
        foreach($sales as $sale) {

            $projectId = $sale->project_id;

            $doProducts = false;
            $doTrackingCodes = true;
            $doWebhooks = false;
            $integration = WooCommerceIntegration::where('project_id', $projectId)->first();
            if(!empty($integration)) {
                $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);

                $service->syncProducts($projectId, $integration, $doProducts, $doTrackingCodes, $doWebhooks);
            }
        }
    }
}
