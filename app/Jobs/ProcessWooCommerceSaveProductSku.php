<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Vinkla\Hashids\Facades\Hashids;

class ProcessWooCommerceSaveProductSku implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $projectId;
    private $productId;
    private $variationId;
    private $data;
    private $tries;

    /**
     * Create a new job instance.
     */
    public function __construct(int $projectId, int $productId, int $variationId, array $data, int $tries=1)
    {
        $this->projectId = $projectId;
        $this->productId = $productId;
        $this->variationId = $variationId;
        $this->data = $data;
        $this->tries = $tries;
    }

    public function handle()
    {
        try{
            
            $integration = WooCommerceIntegration::where('project_id',$this->projectId)->first();
            $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);
            
            $service->woocommerce->post('products/'.$this->productId.'/variations/'.$this->variationId.'/', $this->data);
            

        }catch(Exception $e){
            
            if($this->tries > 0){
                $tries = --$this->tries;
                $this->dispatch($this->projectId, $this->productId, $this->variationId, $this->data, $tries);
            }else{
                //report($e);
            }
            //
        }


    }

    
}
