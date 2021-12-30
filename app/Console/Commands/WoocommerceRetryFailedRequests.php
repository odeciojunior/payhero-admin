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
        $requests = $model->where('status', 0)
            ->whereRaw("DATEDIFF(CURDATE(),STR_TO_DATE(created_at, '%Y-%m-%d')) <= 10")->get();


        $this->line('Total: ' . count($requests));

        foreach ($requests as $request) {

            try {
                $integration = WooCommerceIntegration::where('project_id', $request['project_id'])->first();
                $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);

                if ($request['method'] == 'approve_billet' || $request['method'] == 'ApproveOrder') {

                    $res = $service->approveBillet($request['order'], $request['project_id'], null, false);

                    if (!empty($res->status) && $res->status == 'processing') {
                        $res = json_encode($res);
                        $service->updatePostRequest($request['id'], 1, $res);

                        $this->line('sucess -> status changed to paid on order: ' . $request['order']);
                    } else {

                        $this->line('fail -> requesId: ' . $request['id']);
                    }
                }

                if ($request['method'] == 'CancelOrder' || $request['method'] == 'CancelOrderAntiFraud') {

                    $res = $service->cancelOrder($request['order'], null, false);

                    if (!empty($res->status) && $res->status == 'cancelled') {
                        $res = json_encode($res);
                        $service->updatePostRequest($request['id'], 1, $res);

                        $this->line('sucess -> status changed to cancelled -> order: ' . $request['order']);
                    } else {

                        $this->line('fail -> requesId: ' . $request['id']);
                    }
                }

                if ($request['method'] == 'AddItemsToOrder') {
                    $res = $service->addItemsToOrder($request['sale_id'], null, false);
                    
                    if (!empty($res->id) && $res->id == $request['order']) {
                        $res = json_encode($res);
                        $service->updatePostRequest($request['id'], 1, $res);

                        $this->line('sucess -> item added -> order: ' . $request['order']);
                    } else {

                        $this->line('fail -> requesId: ' . $request['id']);
                    }
                }



            } catch (Exception $e) {

                $this->line('erro -> ' . $e->getMessage());
            }
        }
    }
}
