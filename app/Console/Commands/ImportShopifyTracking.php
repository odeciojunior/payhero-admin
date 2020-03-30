<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\TrackingService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class ImportShopifyTracking extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'importShopifyTracking';
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
        $productService = new ProductService();
        $trackingService = new TrackingService();

        $userToVerify = 147;

        $count = 1;

        $recreatedWebhooks = [];

        $sales = Sale::with([
            'productsPlansSale',
            'plansSales.plan.project.shopifyIntegrations'
        ])->doesntHave('tracking')
            //->where('owner_id', $userToVerify)
            ->where('status', 1)
            ->whereNotNull('shopify_order')
            ->orderByDesc('id')
            ->chunk(100, function ($sales) use (&$count, $productService, $trackingService, &$recreatedWebhooks) {
                foreach ($sales as $sale) {
                    try {
                        $this->line($count . '. Verificando venda: ' . $sale->id);

                        $project = $sale->plansSales
                            ->first()
                            ->plan
                            ->project;

                        $integration = $project->shopifyIntegrations->first();

                        $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);

                        //Recria o webhook
                        /*if(!in_array($project->id, $recreatedWebhooks)){

                            $shopifyService->deleteShopWebhook();

                            $shopifyService->createShopWebhook([
                                "topic"   => "products/create",
                                "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($integration->project_id),
                                "format"  => "json",
                            ]);

                            $shopifyService->createShopWebhook([
                                "topic"   => "products/update",
                                "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($integration->project_id),
                                "format"  => "json",
                            ]);

                            $shopifyService->createShopWebhook([
                                "topic"   => "orders/updated",
                                "address" => 'https://app.cloudfox.net/postback/shopify/' . Hashids::encode($integration->project_id) . '/tracking',
                                "format"  => "json",
                            ]);

                            $this->info('Webhook do projeto recriado');

                            $recreatedWebhooks[] = $project->id;
                        }*/

                        //Verifica os códigos de rastreio que não vieram nos postbacks
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
                                                        $this->info('Tracking criado: ' . $fulfillment->getTrackingNumber());
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
                        } else {
                            $this->warn('Nenhuma realização encontrada no shopify');
                        }
                        $count++;
                    } catch (\Exception $e) {
                        $this->error('ERRO: ' . $e->getMessage());
                    }
                }
            });
        $this->info("ACABOOOOOOOOOOU!");
    }
}
