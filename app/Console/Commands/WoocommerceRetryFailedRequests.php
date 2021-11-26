<?php

namespace App\Console\Commands;

use App\Exceptions\CommandMonitorTimeException;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Modules\Core\Entities\SaleWoocommerceRequests;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;


class WoocommerceRetryFailedRequests extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'command:WoocommerceRetryFailedRequests';
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
        $requests = $model->where('status',0)->get();

        

        foreach ($requests as $request) {
            try {

                if($request['method']=='approve_billet'){

                    $integration = WooCommerceIntegration::where('project_id', $request['project_id'])->first();
    
                    $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);

                    $res = $service->approveBillet($request['order'], $request['project_id']);

                    if(!empty($res->status) && $res->status == 'processing'){
                        $res = json_encode($res);
                        $service->update_post_request($request['id'], 1, $res);
                        
                        $this->line('sucesso: '.$request['id']);
                        
                    }else{
                        
                        $this->line('fail: '.$request['id']);
                    }
                }

                
            } catch (Exception $e) {

                $this->line('erro -> ' . $e->getMessage());
                
            }
        }
    }


}
