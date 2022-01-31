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



class ImportWooCommerceOrders implements ShouldQueue
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
                
                $orders = $service->woocommerce->get('orders', 
                    [
                        'status'=>'completed', 
                        'page'=> $this->page, 
                        'per_page'=>5
                    ]);
                
                if(empty($orders)){
                   
                    return false;

                }else{
                    
                    $service->importTrackingCodes($this->projectId, $orders);
                    
                    $page = $this->page;

                    $page++;
                    
                    $this->dispatch($this->projectId, $this->userId, $page);
                }
                
                return true;
                
            }
            

        } catch (Exception $e) {    

            //report($e);
            
        }
    }
}
