<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use Modules\Core\Services\WooCommerceService;
use Modules\Core\Entities\WooCommerceIntegration;

class CreateWooCommerceWebhooks implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $data;
    private $projectId;

    public function __construct($projectId, $data)
    {
        $this->data = $data;
        $this->projectId = $projectId;
    }

    public function handle()
    {
        try {
            $integration = WooCommerceIntegration::where("project_id", $this->projectId)->first();

            if (!empty($integration)) {
                $woocommerceService = new WooCommerceService(
                    $integration->url_store,
                    $integration->token_user,
                    $integration->token_pass
                );

                $woocommerceService->woocommerce->post("webhooks", $this->data);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
