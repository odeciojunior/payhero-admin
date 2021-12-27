<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\SaleWoocommerceRequests;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;


class WoocommerceReorderSales extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'command:WoocommerceReorderSales';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        
        $model = new SaleWoocommerceRequests();
        $requests = $model->where('status', 0)->whereIn('method',['CreatePendingOrder', 'ProcessWooCommerceOrderCreate'])
            ->whereRaw("DATEDIFF(CURDATE(),STR_TO_DATE(created_at, '%Y-%m-%d')) <= 10")->get();

        $this->line('Total: ' . count($requests));
        
        foreach ($requests as $request) {
            try {
                    $integration = WooCommerceIntegration::where('project_id', $request['project_id'])->first();
                    $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);
                    
                    $data = json_decode($request['send_data'], true);

                    $changeToPaidStatus = 0;

                    if($data['status'] == 'processing' && $data['set_paid'] == true){
                        $data['status'] = 'pending';
                        $data['set_paid'] = false;
                        $changeToPaidStatus = 1;
                    }

                    $result = $service->woocommerce->post('orders', $data);

                    if($result->id){
                        $order = $result->id;
                        $saleModel = Sale::where('id',$request['sale_id'])->first();
                        $saleModel->woocommerce_order = $order;
                        $saleModel->save();
                        
                        $result = json_encode($result);
                        $service->updatePostRequest($request['id'], 1, $result, $order);

                        $this->line('success -> order generated: ' . $order);

                        if($changeToPaidStatus == 1){
                            
                            $result = $service->approveBillet($order, $request['project_id'], $request['sale_id']);

                            if($result->status == 'processing')
                                $this->line('success -> order status changed: ' . $order);

                        }

                    }
            } catch (Exception $e) {

                $this->line('erro -> ' . $e->getMessage());
                
            }
        }
    }
}
