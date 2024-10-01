<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Services\WooCommerceService;
use Modules\Core\Entities\WooCommerceIntegration;

class ImportWooCommerceProductVariation implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $projectId;
    private $userId;
    private $_product;
    private $variationId;

    public function __construct($projectId, $userId, $_product, $variationId)
    {
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->_product = $_product;
        $this->variationId = $variationId;
    }

    public function handle()
    {
        try {
            $integration = WooCommerceIntegration::where("project_id", $this->projectId)->first();

            if (!empty($integration)) {
                $service = new WooCommerceService(
                    $integration->url_store,
                    $integration->token_user,
                    $integration->token_pass
                );

                $variation = $service->woocommerce->get("products/" . $this->variationId);

                $service->importProductVariation($variation, $this->_product, $this->projectId, $this->userId);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
