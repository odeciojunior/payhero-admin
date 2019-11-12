<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Tracking;
use Modules\Core\Services\PerfectLogService;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\TrackingService;
use Vinkla\Hashids\Facades\Hashids;

class VerifyTrackings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:verifyTrackings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //envia emails de atualizacao de tracking
        try {
            $this->line(date('Y-m-d H:i:s') . ' Executando...');
            $salesModel = new Sale();
            $productPlanSaleModel = new ProductPlanSale();
            $trackingModel = new Tracking();
            $trackingService = new TrackingService();
            $perfectLogService = new PerfectLogService();
            $productService = new ProductService();

            //DB::beginTransaction();

            $sales = $salesModel->with(['productsPlansSale', 'client', 'plansSales.plan.productsPlans.product.productsPlanSales.tracking'])
                ->where('status', 1)
                ->whereNotNull('shopify_order')
                ->where('id', '<', 16333)
                ->orderBy('id', 'desc')
                ->get();

            foreach ($sales as $sale) {
                $this->line('Venda: ' . $sale->id . ' procurando postback...');
                $postback = PostbackLog::select('data')
                    ->where('description', 'shopify-tracking')
                    ->whereRaw('JSON_EXTRACT(data, "$.fulfillments[0].tracking_number") IS NOT NULL')
                    ->whereRaw('JSON_EXTRACT(data, "$.id") = ' . $sale->shopify_order)
                    ->orderBy('id', 'desc')
                    ->get()
                    ->map(function ($item) {
                        return json_decode($item->data);
                    })->last();
                if (isset($postback)) {
                    $this->line('POSTBACK ENCONTRADOOOOOOOOOOOOOOOOOOOOOO! Verificando dados...');
                    $saleProducts = $productService->getProductsBySale($sale);
                    foreach ($postback->fulfillments as $fulfillment) {
                        if (!empty($fulfillment->tracking_number)) {
                            //percorre os produtos que vieram no postback
                            foreach ($fulfillment->line_items as $line_item) {
                                //verifica se existem produtos na venda com mesmo variant_id e com mesma quantidade vendida
                                $products = $saleProducts->where('shopify_variant_id', $line_item->variant_id)
                                    ->where('amount', $line_item->quantity);
                                if ($products->count()) {
                                    foreach ($products as &$product) {
                                        try{
                                            $this->line('Procurando tracking...');
                                            $tracking = $trackingModel->find(current(Hashids::decode($product->tracking_id)));
                                            if(!isset($tracking)){
                                                $this->line('Tracking nao encontrado. Criando ...');
                                                $productPlanSale = $productPlanSaleModel->with(['sale.plansSales.plan.productsPlans', 'sale.delivery'])
                                                    ->find($product->product_plan_sale_id);
                                                $tracking = $trackingService->createTracking($fulfillment->tracking_number, $productPlanSale);
                                            }
                                            $this->line('Enviando para a PerfectLog...');
                                            $perfectLogService->track(Hashids::encode($tracking->id), $tracking->tracking_code);
                                        }catch (\Exception $ex){
                                            $this->line('Erro: ' . $ex->getMessage());
                                            Log::error($ex->getMessage());
                                        }
                                    }
                                }
                            }
                        }
                    }
                }else{
                    $this->line('Nenhum postback nao encontrado.');
                }
            }

            //DB::commit();

            $this->line(date('Y-m-d H:i:s') . ' Funcionou paizao!');
        } catch (Exception $e) {
            //DB::rollBack();
            $this->line(date('Y-m-d H:i:s') . ' Error: ' . $e->getMessage());
        }
    }
}
