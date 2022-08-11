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

class ImportWooCommerceProduct implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $projectId;
    private $userId;
    private $_product;

    public function __construct($projectId, $userId, $_product)
    {
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->_product = $_product;
    }

    public function handle()
    {
        try {
            $integration = WooCommerceIntegration::where("project_id", $this->projectId)->first();

            if (!empty($integration)) {
                $woocommerce = new WooCommerceService(
                    $integration->url_store,
                    $integration->token_user,
                    $integration->token_pass
                );

                $woocommerce->importProduct($this->projectId, $this->userId, $this->_product);
            }
        } catch (Exception $e) {
            //report($e);
        }
    }
}
