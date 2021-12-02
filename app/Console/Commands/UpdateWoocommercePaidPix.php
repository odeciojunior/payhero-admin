<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Vinkla\Hashids\Facades\Hashids;

class UpdateWoocommercePaidPix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:update-paid-pix';

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
        $sales = Sale::whereNotNull('woocommerce_order')->where('payment_method', Sale::PIX_PAYMENT)->where('status', Sale::STATUS_APPROVED)->get();

        foreach($sales as $sale) {
            $projectId = $sale->project_id;

            $integration = WooCommerceIntegration::where('project_id', $projectId)->first();
            if(!empty($integration)) {
                $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);

                $service->approvePix($sale->woocommerce_order);
            }
        }
    }
}
