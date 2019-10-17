<?php

namespace App\Http\Controllers\Dev;

use Exception;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\ProductService;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Checkout\Classes\MP;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Domain;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Product;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Transfer;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\DomainRecord;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Services\NotazzService;
use Modules\Core\Services\HotZappService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Entities\ProductPlanSale;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Entities\HotZappIntegration;
use Modules\Core\Entities\ShopifyIntegration;

class TesteController extends Controller
{
    /**
     * @var MP
     */
    private $mp;

    /**
     * PostBackMercadoPagoController constructor.
     */
    public function __construct()
    {
        //
    }

    public function code($code)
    {
        $id       = current(Hashids::decode($code));
        $idSale   = current(Hashids::connection('sale_id')->decode($code));
        $idPusher = current(Hashids::connection('pusher_connection')->decode($code));
        dd('connection("main") = ' . $id, 'connection("sale_id") = ' . $idSale, 'connection("pusher_connection") = ' . $idPusher);
    }

    public function index()
    {
        $this->tgFunction();
        dd('tg');

        $this->mp = new MP(getenv('MERCADO_PAGO_ACCESS_TOKEN_PRODUCTION'));

        //        $requestData = $request->all();
        $requestData = [
            'type' => 'payment',
            'data' => [
                'id' => '5096826632',
            ],
        ];
        /* $postBackLogModel = new PostbackLog();

         $postBackLogModel->create([
                                       'origin'      => 4,
                                       'data'        => json_encode($requestData),
                                       'description' => 'mercado-pago',
                                   ]);*/

        if (isset($requestData['type']) && $requestData['type'] == 'payment') {

            $saleModel        = new Sale();
            $transactionModel = new Transaction();
            $companyModel     = new Company();
            $userModel        = new User();
            $planModel        = new Plan();
            $planSaleModel    = new PlanSale();

            $sale = $saleModel->where('gateway_id', $requestData['data']['id'])->first();

            /*if (empty($sale)) {
                Log::warning('VENDA NÃO ENCONTRADA!!!' . @$requestData['data']['id']);

                $postBackLogModel->create([
                                              'origin'      => 4,
                                              'data'        => json_encode($requestData),
                                              'description' => 'mercado-pago',
                                          ]);

                return response()->json(['message' => 'sale not found'], 200);
            }*/

            $paymentInfo = $this->mp->get('/v1/payments/' . $requestData['data']['id']);

            Log::warning('venda atualizada no mercado pago:  ' . print_r($paymentInfo, true));

            if ($requestData['transaction']['status'] == $sale->gateway_status) {
                return response()->json(['message' => 'success'], 200);
            }

            $transactions = $transactionModel->where('sale', $sale->id)->get();

            if ($requestData['transaction']['status'] == 'paid') {

                date_default_timezone_set('America/Sao_Paulo');

                $sale->update([
                                  'end_date'       => Carbon::now(),
                                  'gateway_status' => 'paid',
                                  'status'         => '1',
                              ]);

                foreach ($transactions as $transaction) {

                    if ($transaction->company != null) {

                        $company = $companyModel->find($transaction->company);

                        $user = $userModel->find($company['user_id']);

                        $transaction->update([
                                                 'status'            => 'paid',
                                                 'release_date'      => Carbon::now()
                                                                              ->addDays($user['release_money_days'])
                                                                              ->format('Y-m-d'),
                                                 'antecipation_date' => Carbon::now()
                                                                              ->addDays($user['boleto_antecipation_money_days'])
                                                                              ->format('Y-m-d'),
                                             ]);
                    } else {
                        $transaction->update([
                                                 'status' => 'paid',
                                             ]);
                    }
                }

                $plansSale = $planSaleModel->where('sale', $sale->id)->first();

                $plan = $planModel->find($plansSale->plan);

                if ($sale->shopify_order != '') {

                    $shopifyIntegrationModel = new ShopifyIntegration();

                    $shopifyIntegration = $shopifyIntegrationModel->where('project', $plan->project)->first();

                    try {
                        $credential = new PublicAppCredential($shopifyIntegration['token']);

                        $client = new Client($credential, $shopifyIntegration['url_store'], [
                            'metaCacheDir' => './tmp',
                        ]);

                        $client->getTransactionManager()->create($sale->shopify_order, [
                            "kind" => "capture",
                        ]);
                    } catch (Exception $e) {
                        Log::warning('erro ao alterar estado do pedido no shopify com a venda ' . $sale->id);
                        report($e);
                    }
                }

                try {
                    $hotZappIntegrationModel = new HotZappIntegration();

                    $hotzappIntegration = $hotZappIntegrationModel->where('project_id', $plan->project)->first();

                    if (!empty($hotzappIntegration)) {

                        $hotZappService = new HotZappService($hotzappIntegration->link);

                        $plansSale = $planSaleModel->where('sale', $sale->id)->get();

                        $plans = [];
                        foreach ($plansSale as $planSale) {

                            $plan = $planModel->find($planSale->plan);

                            $plans[] = [
                                "price"        => $plan->price,
                                "quantity"     => $planSale->amount,
                                "product_name" => $plan->name,
                            ];
                        }

                        $hotZappService->newBoleto($sale, $plans);
                    }
                } catch (Exception $e) {
                    Log::warning('erro ao enviar notificação pro HotZapp na venda ' . $sale->id);
                    report($e);
                }
            } else {

                if ($requestData['transaction']['status'] == 'chargedback') {
                    $sale->update([
                                      'gateway_status' => 'chargedback',
                                      'status'         => '4',
                                  ]);

                    $transferModel = new Transfer();

                    foreach ($transactions as $transaction) {

                        if ($transaction->status == 'transfered') {
                            $company = $companyModel->find($transaction->company);

                            $transferModel->create([
                                                       'transaction' => $transaction->id,
                                                       'user'        => $company->user_id,
                                                       'value'       => $transaction->value,
                                                       'type'        => 'out',
                                                   ]);

                            $company->update([
                                                 'balance' => $company->balance -= $transaction->value,
                                             ]);
                        }

                        $transaction->update([
                                                 'status' => 'chargedback',
                                             ]);
                    }
                } else {
                    $sale->update([
                                      'gateway_status' => $requestData['transaction']['status'],
                                  ]);

                    foreach ($transactions as $transaction) {
                        $transaction->update(['status' => $requestData['transaction']['status']]);
                    }
                }
            }
        }

        return response()->json(['message' => 'success'], 200);
    }

    public function indexx()
    {
        $this->tgFunction();
        /*$dataValue = [
            'type' => 'payment',

            'data' => [
                'id' => '113923781',
            ],
        ];

        return redirect()->route('dev.cloudfox.com.br/postback/mercadopago', compact('data', $dataValue));*/
    }

    public function jeanFunction()
    {
//        //update sem where! popula a coluno sub_total
//        try {
//            DB::beginTransaction();
//
//            DB::statement('update sales s
//            set s.sub_total =
//            (select sum(cast((cast(plan_value as decimal(8,2)) * cast(amount as signed)) as decimal(8,2))) as sub_total
//            from plans_sales ps
//            where ps.sale_id = s.id) where 1=1');
//
//            DB::commit();
//
//            return "Ok!";
//        } catch (Exception $e) {
//            DB::rollBack();
//            dd($e);
//        }
    }

    public function julioFunction()
    {
        $checkoutModel = new Checkout();

        $checkouts = $checkoutModel->where('email_sent_amount', '>' , 10)->get();

        foreach($checkouts as $checkout){
            $checkout->update([
                'email_sent_amount' => '4'
            ]);
        }

        $checkouts = $checkoutModel->where('sms_sent_amount', '>' , 10)->get();

        foreach($checkouts as $checkout){
            $checkout->update([
                'sms_sent_amount' => '2'
            ]);
        }

    }

    public function parseToArray($xpath, $class)
    {
        //        $xpathquery = "//a[@class='" . $class . "']";
        $xpathquery = "//a";
        $elements   = $xpath->query($xpathquery);

        if (!is_null($elements)) {
            $resultarray = [];
            foreach ($elements as $element) {
                $nodes = $element->childNodes;
                foreach ($nodes as $node) {
                    $resultarray[] = $node->nodeValue;
                }
            }

            return $resultarray;
        }
    }

    /**
     * Funcao utilizada pelo tg
     */
    public function tgFunction()
    {
        //nada

        $shopifyIntegrationModel = new ShopifyIntegration();

        $integrations = $shopifyIntegrationModel->has('project')->get();

        dd($integrations);

        //Sérgio Delmutti Ramos da Silva DPVYB34LEL3KzkJ

        //         $saleModel = new Sale();
        //
        //         $sales = $saleModel->whereHas('client', function($query){
        //             $query->where('name','LIKE', 'UIARA VAZ');
        //         })->get();
        //         dd($sales);

//        $notazzInvoice = new NotazzInvoice();

//        $invoice  = $notazzInvoice->whereHas('sale', function($querySale) {
//            $querySale->whereHas('client', function($queryClient) {
//                $queryClient->where('name', 'LIKE', 'UIARA VAZ');
//            });
//        })->get();
//        dd($invoice);
        $nservice = new NotazzService();
        dd($nservice->consultNfse(459));
        //
        //        $sale = $saleModel->with(['project', 'project.notazzIntegration'])->find(3366);
        //
        //         $nservice->createInvoice($sale->project->notazzIntegration->id, $sale->id, 1);

        //$tokenApi = $nservice->createOldInvoices($sale->project->id,'2018-09-18');

        //dd($nservice->checkCity('wNiRmZ2EGZ2EWN5MjYzEGMwITZjRGO4cTO2QGZlBzNyoHd14ke5QVMuVWYkFDZhRjZkVGMzIzM0YGZ3kTM4AzM1U2N1IzN4EGMnZ', 'SP', 'Amparo'));

        //$shopifyService = new ShopifyService('morena-orange.myshopify.com', '649e81ebe2c99f68ba4c7a3048bdaba4');
        //$shopifyService->deleteShopWebhook();
        //$shopifyService->createShopifyIntegrationWebhook(188, "https://app.cloudfox.net/postback/shopify/");
        //dd($shopifyService->getShopWebhook());
        //$shopifyService->importShopifyStore(154, auth()->user()->id);
        //        $shopifyService->setThemeByRole('main');
        //        $htmlCart = $shopifyService->getTemplateHtml('snippets/ajax-cart-template.liquid');
        //        $shopifyService->updateTemplateHtml('snippets/ajax-cart-template.liquid', $htmlCart, 'junbotron.cf', true);

        //        $htmlBody = $shopifyService->getTemplateHtml('layout/theme.liquid');
        //        if ($htmlBody) {
        //            //template do layout
        //
        //            $shopifyService->insertUtmTracking('layout/theme.liquid', $htmlBody);
        //        }

        /*
        $nservice  = new NotazzService();
        $saleModel = new Sale();

        $sale = $saleModel->with(['projectModel', 'projectModel.notazzIntegration'])->find(3366);

        $nservice->createInvoice($sale->projectModel->notazzIntegration->id, $sale->id, 1);
        */

        //$shopifyService = new ShopifyService('lipo-duo.myshopify.com', 'd7a27718b291b2e835d2e7d6c3a4787e');

        //                $shopifyService->createShopWebhook([
        //                                             "topic"   => "orders/updated",
        //                                             "address" => 'https://app.cloudfox.net/postback/shopify/YKV603kndgw8ymD/tracking',
        //                                             "format"  => "json",
        //                                         ]);

        //$shopifyService->deleteShopWebhook('688952344673');
        //dd($shopifyService->getShopWebhook());

        //        $shopifyService->createShopWebhook([
        //                                     "topic"   => "orders/updated",
        //                                     "address" => 'https://eca0ccd1.ngrok.io/postback/shopify/da6pVgdQ63k7BW0/tracking',
        //                                     "format"  => "json",
        //                                 ]);

        dd('aa');
    }

    public function joaoLucasFunctionDomain()
    {
        $domainModel       = new Domain();
        $domainRecordModel = new DomainRecord();

        $domains = $domainModel->whereNull('cloudflare_domain_id')->get();

        $cloudFlareService = new CloudFlareService();

        /*foreach ($domains as $domain) {
            if (!empty($domain)) {

                $domainCloudflare = $cloudFlareService->getZones($domain->name);
                if (!empty($domainCloudflare)) {

                    foreach ($domainCloudflare as $dom) {

                        $domain->update([
                                            'cloudflare_domain_id' => $dom->id,
                                        ]);
                    }
                } else {
                    $domainRecordModel->where('domain_id', $domain->id)->delete();
                    $domain->delete();
                }
            }
        }*/

        $domainRecords = $domainRecordModel->with('domain')->where('cloudflare_record_id', '')->get();

        foreach ($domainRecords as $domainRecord) {
            if (empty($domainRecord->cloudflare_record_id)) {
                if (empty($domainRecord->domain)) {
                } else {

                    $domainRecordCloudflare = $cloudFlareService->getRecords($domainRecord->domain->name);
                    foreach ($domainRecordCloudflare as $item) {
                        if ($domainRecord->type == $item->type && $domainRecord->name == $item->name && $domainRecord->content == $item->content) {

                            $domainRecord->update([
                                                      'cloudflare_record_id' => $item->id,
                                                  ]);
                        }
                    }
                }
            }
        }

        dd($domainRecords = $domainRecordModel->with('domain')->get());
        /*$domainRecordModel->where('domain_id', $domain->id)->update([
                                                                        'cloudflare_record_id' => $domainCloudflare->id,
                                                                    ]);*/
    }

    /**
     * Funcao para remover caracteres especiais de produtos shopify
     */
    public function removeSpecialCharacter()
    {
        $productsModel = new Product();

        $productsSearch = $productsModel->where('shopify', 1)->get();
        foreach ($productsSearch as $product) {
            $product->update([
                                 'name'        => preg_replace('/[^a-zA-Z0-9_ -]/s', '', substr($product->name, 0, 100)),
                                 'description' => preg_replace('/[^a-zA-Z0-9_ -]/s', '', substr($product->description, 0, 100)),
                             ]);
        }
    }

    /**
     * @param Request $request
     */
    public function thalesFunction(Request $request)
    {
        //        (new BoletoService())->verifyBoletoPaid();
        /** @var Sale $saleModel */
        $saleModel = new Sale();
        /** @var Carbon $date */
        $start   = now()->startOfDay()->subDays(70);//->toDateString();
        $end     = now()->endOfDay()->subDays(70);//->toDateString();
        $boletos = $saleModel->newQuery()
                             ->with(['client', 'plansSales.plan.products'])
                             ->whereBetween('start_date', [$start, $end])
                             ->where(
                                 [
                                     ['payment_method', '=', '2'],
                                     ['status', '=', '2'],
                                 ]
                             )->get();
        dd($start, $end, $boletos);
    }

    public function joaoLucasFunction()
    {
        $pixels = Pixel::where('platform', 'like', '%google%')->get();

        foreach ($pixels as $pixel) {
            $pixel->update([
                               'platform' => 'google_adwords',
                           ]);
        }

        dd($pixels);
    }

    /**
     * Funcao tracking code
     */
    public function trackingCodeFunction()
    {
        $saleModel            = new Sale();
        $productPlanSaleModel = new ProductPlanSale();
        $sales                = $saleModel->whereHas('delivery', function($query) {
            $query->where('tracking_code', '!=', null);
        })->with('delivery', 'productsPlansSale')->get();
        foreach ($sales as $sale) {
            foreach ($sale->productsPlansSale as $product) {
                $product->update([

                                     'tracking_status_enum' => $productPlanSaleModel->present()
                                                                                    ->getStatusEnum('posted'),
                                     'tracking_code'        => $sale->delivery->tracking_code,
                                 ]);
            }
        }
        return 'Pronto!';
    }
}


