<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Vinkla\Hashids\Facades\Hashids;

class FillShopifyProductSKU extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fillShopifyProductSKU';

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
        try {
            $this->line(date('Y-m-d H:i:s') . ' Executando...');

            $shopifyIntegrationsModel = new ShopifyIntegration();
            $productsModel = new Product();

            DB::beginTransaction();

            $integrations = $shopifyIntegrationsModel::all();
            $count = 1;
            foreach ($integrations as $integration){
                $shopifyService = new ShopifyService($integration->url_store, $integration->token);
                $products = $productsModel->where('shopify', 1)
                    //->whereNull('sku')
                    ->whereNotNull('shopify_variant_id')
                    ->where('project_id', $integration->project_id)
                    ->get();
                $this->line('Loja: "' . $integration->url_store . '" ' . $products->count() . ' produtos encontrados.');
                foreach ($products as $product){
                    try {
                        $shopifyProduct = $shopifyService->getProductVariant($product->shopify_variant_id);
                    }catch (Exception $e){
                        $shopifyProduct = null;
                    }
                    if(isset($shopifyProduct) && $shopifyProduct->getSku()){
                        $this->line($count . '. Adicionando SKU: "' . $shopifyProduct->getSku() . '" ao produto: "' . $product->name . '"');
                        $product->update(['sku' => $shopifyProduct->getSku()]);
                        $count++;
                    }
                }
            }

            DB::commit();

            $this->line(date('Y-m-d H:i:s') . ' Funcionou paizao!');
        } catch (Exception $e) {
            DB::rollBack();
            $this->line(date('Y-m-d H:i:s') . ' Error: ' . $e->getMessage());
        }
    }
}
