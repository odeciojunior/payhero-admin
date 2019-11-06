<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProductService;
use Vinkla\Hashids\Facades\Hashids;

class SendTrackingUpdateLast10Days extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendTrackingUpdateLast10Days';

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
            $productService = new ProductService();

            DB::beginTransaction();

            $postbacks = PostbackLog::select('data')
                ->where('description', 'shopify')
                ->where('created_at', '>=', (new Carbon())->subDays(10))
                ->whereRaw('JSON_EXTRACT(data, "$.fulfillments[0].tracking_number") IS NOT NULL')
                ->get()
                ->map(function ($item) {
                    return json_decode($item->data);
                });

            $sales = $salesModel->with(['productsPlansSale', 'client'])
                ->where('status', 1)
                ->whereHas('productsPlansSale', function ($query) {
                    $query->whereNull('tracking_code');
                })
                ->where('created_at', '>=', (new Carbon())->subDays(10))
                ->get();

            $count = 1;
            foreach ($sales as $sale) {
                $postback = $postbacks->where('id', $sale->shopify_order)->last();
                if (isset($postback)) {
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
                                        //caso exista, verifica se o codigo que de rastreio que veio no postback e diferente
                                        //do que esta na tabela
                                        $productPlanSale = $productPlanSaleModel->find($product->product_plan_sale_id);
                                        if (isset($productPlanSale)) {
                                            //atualiza o registro e dispara o e-mail
                                            //$productPlanSale->update(['tracking_code' => $fulfillment->tracking_number]);
                                            //atualiza no array de produtos para enviar no email
                                            $product->tracking_code = $fulfillment->tracking_number;
                                            $this->line($count . ". Enviando e-mail para: " . $sale->client->email);
                                            event(new TrackingCodeUpdatedEvent($sale, $productPlanSale, $saleProducts));
                                            $count++;
                                        }
                                    }
                                }
                            }
                        }
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
