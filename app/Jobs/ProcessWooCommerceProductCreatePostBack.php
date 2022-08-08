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

class ProcessWooCommerceProductCreatePostBack implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        try {
            $wooCommerceIntegration = WooCommerceIntegration::where("project_id", $this->request->project_id)->first();

            $wooCommerceService = new WooCommerceService(
                $wooCommerceIntegration->url_store,
                $wooCommerceIntegration->token_user,
                $wooCommerceIntegration->token_pass
            );

            $variationId = !empty($this->request->parent_id) ? $this->request->id : null;

            $sku = $wooCommerceService->createProduct(
                $wooCommerceIntegration->project_id,
                $wooCommerceIntegration->user_id,
                $this->request,
                $this->request->description,
                $variationId
            );

            if (!empty($sku)) {
                $data = [
                    "sku" => $sku,
                ];
                if (empty($this->request->parent_id) && empty($this->request->variations)) {
                    $wooCommerceService->woocommerce->post("products/" . $this->request->id, $data);
                } else {
                    $wooCommerceService->woocommerce->post(
                        "products/" . $this->request->parent_id . "/variations/" . $this->request->id,
                        $data
                    );
                }
            }
        } catch (Exception $e) {
            // if(stristr('JSON ERROR: Syntax error', $e) || stristr('SKU', $e)){
            //     //loja retornou json inválido OU variação repetida
            // }else{
            //     report($e);
            // }
        }
    }
}
