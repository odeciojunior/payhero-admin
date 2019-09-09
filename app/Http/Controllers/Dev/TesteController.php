<?php

namespace App\Http\Controllers\Dev;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\DomainRecord;
use Modules\Core\Entities\HotZappIntegration;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\User;
use Modules\Core\Services\CloudFlareService;
use Modules\Core\Services\HotZappService;
use Modules\Core\Services\NotazzService;
use Modules\Core\Services\ShopifyService;
use Modules\Checkout\Classes\MP;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Slince\Shopify\Client;
use Slince\Shopify\PublicAppCredential;
use Exception;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

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
        ///
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
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
                } catch (\Exception $e) {
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

    public function julioFunction()
    {

        $plans = Plan::whereNotNull('shopify_variant_id')->get();

        foreach ($plans as $plan) {

            $product = $plan->products->first();

            if (!empty($product)) {
                $product->update([
                                     'shopify_id'         => $plan->shopify_id,
                                     'shopify_variant_id' => $plan->shopify_variant_id,
                                 ]);
            }
        }

        dd('heyy');
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

        $shopifyService = new ShopifyService('joaolucasteste1.myshopify.com', '465599868002dc3194ed778d7ea1a1ff');

        $shopifyService->setThemeByRole('main');
        $htmlBody = $shopifyService->getTemplateHtml('layout/theme.liquid');
        if ($htmlBody) {
            //template do layout

            $shopifyService->insertUtmTracking('layout/theme.liquid', $htmlBody);
        }

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

    public function joaoLucasFunction()
    {/*
        $productsModel    = new Product();
        $productPlanModel = new ProductPlan();
        $planModel        = new Plan();

        $products = $productsModel->WhereNotNull('shopify_id')->whereNull('project_id')->get();
        foreach ($products as $product) {
            $productPlan = $productPlanModel->where('product_id', $product->id)->first();
            if (!empty($productPlan)) {

                $plan = $planModel->find($productPlan->plan_id);

                $product->update(
                    [
                        'project_id' => $plan->project_id,
                    ]
                );
            }
        }

        dd("hey");*/
    }
}


