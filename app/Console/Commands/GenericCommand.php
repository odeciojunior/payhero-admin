<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ProjectNotificationService;
use Illuminate\Support\Carbon;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\TrackingService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic';
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

    /**
     *
     */
    public function handle()
    {
        $count = 1;
        $productService = new ProductService();
        $trackingService = new TrackingService();

        try {

            $sales = Sale::with([
                'productsPlansSale',
                'plansSales.plan.project.shopifyIntegrations'
            ])->doesntHave('tracking')
                ->where('owner_id', 557)
                ->where('status', 1)
                ->whereNotNull('shopify_order')
                ->chunk(100, function ($sales) use ($count, $productService, $trackingService) {
                    foreach ($sales as $sale) {

                        $this->line($count . '. Verificando venda: ' . $sale->id);

                        $project = $sale->plansSales
                            ->first()
                            ->plan
                            ->project;

                        $integration = $project->shopifyIntegrations->first();

                        $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);

                        $fulfillments = $shopifyService->findFulfillments($sale->shopify_order);

                        if (!empty($fulfillments)) {
                            //obtem os produtos da venda
                            $saleProducts = $productService->getProductsBySale($sale);
                            foreach ($fulfillments as $fulfillment) {
                                if (!empty($fulfillment->getTrackingNumber())) {
                                    //percorre os produtos que vieram no postback
                                    foreach ($fulfillment->getLineItems() as $lineItem) {
                                        //verifica se existem produtos na venda com mesmo variant_id e com mesma quantidade vendida
                                        $products = $saleProducts->where('shopify_variant_id', $lineItem->getVariantId())
                                            ->where('amount', $lineItem->getQuantity());
                                        if ($products->count()) {
                                            foreach ($products as &$product) {
                                                //caso exista, verifica se o codigo que de rastreio que veio no postback e diferente
                                                //do que esta na tabela
                                                $productPlanSale = $sale->productsPlansSale->find($product->product_plan_sale_id);

                                                DB::beginTransaction();
                                                activity()->disableLogging();
                                                $tracking = $trackingService->createTracking($fulfillment->getTrackingNumber(), $productPlanSale);
                                                activity()->enableLogging();
                                                if (!empty($tracking)) {
                                                    $apiTracking = $trackingService->sendTrackingToApi($tracking);
                                                    if (!empty($apiTracking)) {
                                                        DB::commit();
                                                        //atualiza no array de produtos para enviar no email
                                                        $product->tracking_code = $fulfillment->getTrackingNumber();
                                                        event(new TrackingCodeUpdatedEvent($sale, $tracking, $saleProducts));
                                                    } else {
                                                        DB::rollBack();
                                                    }
                                                } else {
                                                    DB::rollBack();
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                        }
                        $count++;
                    }
                });
            $this->info("ACABOOOOOOOOOOU!");
        } catch (\Exception $e) {
            $this->info($count . ' executaram com sucesso!');
            $this->error($e->getMessage());
        }
    }
}
