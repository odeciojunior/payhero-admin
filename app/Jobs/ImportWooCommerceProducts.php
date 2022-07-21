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



class ImportWooCommerceProducts implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $projectId;
    private $userId;
    private $page;

    public function __construct($projectId, $userId, $page)
    {
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->page = $page;
    }

    public function handle()
    {
        try {
            
            
            $integration = WooCommerceIntegration::where('project_id', $this->projectId)->first();

            if(!empty($integration)){

                $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);
                
                //first checkpoint
                try {
                    $products = $service->woocommerce->get('products', ['status'=>'publish', 'page'=> $this->page, 'per_page'=>1]);
                } catch (\Throwable $th) {
                    //$woocommerceSyinc = new WooCommerceIntegration()
                }
                
                if(empty($products)){
                   
                    return false;

                }else{
                    
                    $service->importProducts($this->projectId, $this->userId, $products);
                    
                    $page = $this->page;

                    $page++;
                    
                    sleep(10);
                    
                    $this->dispatch($this->projectId, $this->userId, $page);
                }
                
                return true;
                
                

            }

            

            

        } catch (Exception $e) {
            
            

            //report($e);

            
        }
    }
}
