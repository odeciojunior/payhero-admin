<?php

namespace App\Http\Controllers\Dev;

use App\Jobs\SendNotazzInvoiceJob;
use Exception;
use Jenssegers\Agent\Facades\Agent;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Entities\NotazzSentHistory;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\SentEmail;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Entities\UserNotification;
use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\CurrencyQuotationService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\IpService;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\SendgridService;
use Modules\Core\Services\TrackingService;
use Modules\Core\Services\UserNotificationService;
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
use Slince\Shopify\Manager\Shop\Shop;
use Spatie\Permission\Models\Role;
use stdClass;
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

    public function index(Request $request)
    {

        $this->tgFunction($request);
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
                            "gateway" => "cloudfox",
                            "authorization" => Hashids::connection('sale_id')->encode($sale->id),
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

    public function jeanFunction(Request $request)
    {
        try {
            $data = $request->all();
            if (!empty($data['id'])) {

                if (is_numeric($data['id'])) {
                    strlen($data['id'] > 8)
                        ? dd(Hashids::encode($data['id']))
                        : dd(Hashids::connection('sale_id')->encode($data['id']));
                } else {
                    strlen($data['id'] > 8)
                        ? dd(current(Hashids::decode($data['id'])))
                        : dd(current(Hashids::connection('sale_id')->decode($data['id'])));
                }
            } else {
                dd('Não rolou!');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function julioFunction()
    {
        $checkoutModel = new Checkout();

        $checkouts = $checkoutModel->where('email_sent_amount', '>', 10)->get();

        foreach ($checkouts as $checkout) {
            $checkout->update([
                                  'email_sent_amount' => '4',
                              ]);
        }

        $checkouts = $checkoutModel->where('sms_sent_amount', '>', 10)->get();

        foreach ($checkouts as $checkout) {
            $checkout->update([
                                  'sms_sent_amount' => '2',
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
    public function tgFunction($request)
    {

        //        $checkser = new CheckoutService();
        //
        //
        //        $sale = Sale::find(191753);
        //
        //        $checkser->cancelPayment($sale, 000);
        //
        //        $shopifyIntegration = ShopifyIntegration::find(154);
        //
        //        //$this->saleId = $sale->id;
        //        $credential   = new PublicAppCredential($shopifyIntegration->token);
        //
        //        $client = new Client($credential, $shopifyIntegration->url_store, [
        //            'metaCacheDir' => '/var/tmp',
        //        ]);
        //        $order  = $client->getOrderManager()->find(1946397605933);

        dd('xXx');

        //        $saleModel     = new Sale();
        //        $planSaleModel = new PlanSale();
        //        $planModel     = new Plan();
        //
        //        $requestData = $request->all();
        //
        //        if(isset($requestData['boleto']))
        //        {
        //
        //            $sales = $saleModel->where('status', 1)
        //                               ->where('payment_method', 2)
        //                               ->where('end_date', '>', '2019-12-21')
        //                               ->paginate(500, ['*'], 'page', $requestData['page']);
        //
        //            dd($sales->count());
        //
        //            foreach ($sales as $sale) {
        //
        //                $plansSale = $planSaleModel->where('sale_id', $sale->id)->first();
        //                $plan      = $planModel->find($plansSale->plan_id);
        //
        //                event(new BilletPaidEvent($plan, $sale, $sale->customer));
        //            }
        //
        //            dd('Fim');
        //
        //        }
        //
        //        dd('what');

        //event(new BilletPaidEvent($plan, $sale, $sale->customer));

        //---------------------------------------------- chargeback
        //                        $transferModel = new Transfer();
        //                        $saleModel     = new Sale();
        //
        //                        $saleId = current(Hashids::connection('sale_id')->decode('OGYoBa3K'));
        //
        //                        $sale = $saleModel->with(['transactions.company', 'project.shopifyIntegrations'])->find($saleId);
        //
        //                        $shopifyIntegration = $sale->project->shopifyIntegrations->where('status', 2)->first();
        //
        //                        try {
        //                            $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
        //                            $shopifyService->refundOrder($shopifyIntegration, $sale);
        //                        } catch (Exception $ex) {
        //
        //                        }
        //
        //                        $sale->update([
        //                                          'gateway_status' => 'chargedback',
        //                                          'status'         => '4',
        //                                      ]);
        //
        //                        foreach ($sale->transactions as $transaction) {
        //
        //                            if ($transaction->status == 'transfered') {
        //
        //                                $transferModel->create([
        //                                                           'transaction_id' => $transaction->id,
        //                                                           'user_id'        => $transaction->company->user_id,
        //                                                           'value'          => $transaction->value,
        //                                                           'type'           => 'out',
        //                                                           'reason'         => 'chargedback',
        //                                                           'company_id'     => $transaction->company->id,
        //                                                       ]);
        //
        //                                $transaction->company->update([
        //                                                                  'balance' => $transaction->company->balance -= $transaction->value,
        //                                                              ]);
        //                            }
        //
        //                            $transaction->update([
        //                                                     'status' => 'chargedback',
        //                                                 ]);
        //                        }
        //
        //                        dd('chargeback feito');

        //---------------------------------------------- chargeback

        /*
                $notazzInvoiceModel       = new NotazzInvoice();
                $notazzSentHistoryModel   = new NotazzSentHistory();
                $saleModel                = new Sale();
                $productPlanModel         = new ProductPlan();
                $currencyQuotationService = new CurrencyQuotationService();

        $saleModel = new Sale();

        $sale = $saleModel->with(['plansSales', 'transactions'])->find(105195);

        if ($sale) {
            //venda encontrada

            $sale = $saleModel->with(['plansSales', 'transactions.company.user', 'project'])->find($sale->id);

            $productsSale = collect();
            foreach ($sale->plansSales as $planSale) {
                foreach ($planSale->plan->productsPlans as $productPlan) {

                    $product = $productPlan->product()->first();

                    if (!empty($productPlan->cost)) {
                        //pega os valores de productplan
                        $product['product_cost']       = preg_replace("/[^0-9]/", "", $productPlan->cost);
                        $product['product_cost']       = (is_numeric($product['product_cost'])) ? $product['product_cost'] : 0;
                        $product['currency_type_enum'] = $productPlan->currency_type_enum;
                    } else {
                        //pega os valores de produto
                        if (!empty($product->cost)) {
                            $product['product_cost'] = preg_replace("/[^0-9]/", "", $product->cost);
                            $product['product_cost'] = (is_numeric($product['product_cost'])) ? $product['product_cost'] : 0;
                        } else {
                            $product['product_cost'] = 0;
                        }

                        $product['currency_type_enum'] = $product->currency_type_enum ?? 1;
                    }

                    $product['product_amount'] = ($planSale->amount * $productPlan->amount) ?? 1;

                    $productsSale->add($product);
                }
            }

            $products = $productsSale;

            if ($products) {
                $costTotal = 0;
                foreach ($products as $product) {

                    if ($product['currency_type_enum'] == $productPlanModel->present()->getCurrency('USD')) {
                        //moeda USD
                        $lastUsdQuotation        = $currencyQuotationService->getLastUsdQuotation();
                        $product['product_cost'] = (int) round(($product['product_cost'] * ($lastUsdQuotation->value / 100)));
                    }

                    $costTotal += (int) ($product['product_cost'] * $product['product_amount']);
                }

                $shippingCost = preg_replace("/[^0-9]/", "", $sale->shipment_value);

                $subTotal = preg_replace("/[^0-9]/", "", $sale->sub_total);

                $discountPlataformTax = $sale->project->notazzIntegration->discount_plataform_tax_flag ?? false;
                if ($discountPlataformTax == true) {

                    foreach ($sale->transactions as $transaction) {
                        if ((!empty($transaction->company)) && ($transaction->company->user->id == $sale->owner_id)) {
                            //plataforma
                            $trasactionRate = preg_replace("/[^0-9]/", "", $transaction->transaction_rate);
                            $costTotal      += (int) $trasactionRate;
                            $costTotal      += (int) (($subTotal + $shippingCost) * ($transaction->percentage_rate / 100));

                            $installmentTaxValue = $sale->installment_tax_value ?? 0;
                            $costTotal           += (int) ($installmentTaxValue);
                        }
                    }
                }

                $baseValue = ($subTotal + $shippingCost) - $costTotal;

                $totalValue = substr_replace($baseValue, '.', strlen($baseValue) - 2, 0);
            }
        }

        dd($totalValue);
*/
        //dd('aa');

        //                //nada
        //                $notazInvoiceModel = new NotazzInvoice();
        //                $nservice          = new NotazzService();
        //
        //                $invoices = $notazInvoiceModel->whereIn('notazz_integration_id', [4, 5, 6])
        //                                              ->where('status', '=', 2)
        //                                              ->limit(50)
        //                                              ->get();
        //
        //                try {
        //                    $count = 0;
        //                    foreach ($invoices as $invoice) {
        //                        if ($count > 90) {
        //                            break;
        //                        }
        //                        $ret = $nservice->deleteNfse($invoice->id);
        //                        if ($ret == false) {
        //                            $invoice->update([
        //                                                 'status' => 5,
        //                                             ]);
        //                            continue;
        //                        }
        //
        //                        $invoice->update([
        //                                             'status'           => 5,
        //                                             'return_message'   => $ret->statusProcessamento,
        //                                             'return_http_code' => $ret->codigoProcessamento,
        //                                         ]);
        //
        //                        $count = $count + 1;
        //                    }
        //
        //                    dd('ok');
        //                } catch (Exception $ex) {
        //                    dd($ex);
        //                }
        //
        //                dd($invoices);

        dd('aa');
    }

    /**
     * Funcao para remover caracteres especiais de produtos shopify
     */
    public function removeSpecialCharacter()
    {
        /*$productsModel = new Product();

        $productsSearch = $productsModel->where('shopify', 1)->get();
        foreach ($productsSearch as $product) {
            $product->update([
                                 'name'        => preg_replace('/[^a-zA-Z0-9_ -]/s', '', substr($product->name, 0, 100)),
                                 'description' => preg_replace('/[^a-zA-Z0-9_ -]/s', '', substr($product->description, 0, 100)),
                             ]);
        }*/
    }

    public function joaoLucasFunction()
    {
        $sales = Sale::whereDate('created_at', '>=', '2020-01-20 15:00:00.0')->whereNotNull('shopify_order')
                     ->whereDate('created_at', '<=', '2020-01-21 13:00:00.0')->get();

        foreach ($sales as $sale) {
            $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();

            if(!empty($shopifyIntegration)){

                $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);
                $sh = $shopifyService->updateOrder($sale);

            }

        }

        dd('Terminou!');



    }

    /**
     * Funcao tracking code
     */
    public function trackingCodeFunction()
    {
        /*$saleModel            = new Sale();
        $productPlanSaleModel = new ProductPlanSale();
        $sales                = $saleModel->whereHas('delivery', function($query) {
            $query->where('tracking_code', '!=', null);
        })->with('delivery', 'productsPlansSale')->get();
        foreach ($sales as $sale) {
            foreach ($sale->productsPlansSale as $product) {
                $product->update([

                                     'tracking_status_enum' => $productPlanSaleModel->present()
                                                                                    ->getTrackingStatusEnum('posted'),
                                     'tracking_code'        => $sale->delivery->tracking_code,
                                 ]);
            }
        }

        return 'Pronto!';*/
    }

    /**
     * @param Request $request
     * @return int|string
     */
    public function faustoFunction(Request $request)
    {
        User::create(
            [
                'name'                                => "Fausto Marins",
                'email'                               => "faustogmjr@gmail.com",
                'email_verified'                      => "1",
                'password'                            => bcrypt("vpc10"),
                'remember_token'                      => "",
                'cellphone'                           => "24999309321",
                'cellphone_verified'                  => "1",
                'document'                            => "",
                'zip_code'                            => "27521130",
                'country'                             => "BR",
                'state'                               => "Rio de Janeiro",
                'city'                                => "Resende",
                'neighborhood'                        => "",
                'street'                              => "",
                'number'                              => "",
                'complement'                          => "",
                'photo'                               => "",
                'date_birth'                          => "1997-08-14",
                'address_document_status'             => "3",
                'personal_document_status'            => "3",
                'score'                               => "",
                'sms_zenvia_amount'                   => "1",
                'percentage_rate'                     => "",
                'transaction_rate'                    => "",
                'foxcoin'                             => "",
                'email_amount'                        => "",
                'call_amount'                         => "",
                'boleto_antecipation_money_days'      => "1",
                'credit_card_antecipation_money_days' => "1",
                'release_money_days'                  => "1",
                'percentage_antecipable'              => "1",
                'antecipation_tax'                    => "1",
                'invites_amount'                      => "1",
                'installment_tax'                     => "1",
                'credit_card_release_money_days'      => "1",
                'boleto_release_money_days'           => "1",
                'boleto_tax'                          => "1",
                'credit_card_tax'                     => "1",
            ]
        );
        if (isset($request->email)) {
            dump(__METHOD__, "Email");
            $email = $request->email ?? null;
            if (FoxUtils::isEmpty($email)) {
                dd("Email vazio.");
            }

            $sentEmails = SentEmail::where("to_email", 'LIKE', '%' . $email . '%')->get()->toArray();
            dump($sentEmails);

            dd("Fim");
        }

        $user = auth()->user() ?? null;
        /** @var UserNotificationService $userNotificationService */
        $userNotificationService = app(UserNotificationService::class);
        dd($userNotificationService->verifyUserNotification($user, "shopify"));
        dd(__FUNCTION__, __METHOD__, __CLASS__);
        dump(__METHOD__);
        try {
            $cardBrand = '';

            $cardNumber = preg_replace('/\D/', '', $request->cardNumber);

            $brands = [
                'visa'      => '/^4\d{12}(\d{3})?$/',
                'master'    => '/^(5[1-5]\d{4}|677189)\d{10}$/',
                'diners'    => '/^3(0[0-5]|[68]\d)\d{11}$/',
                'discover'  => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
                'elo'       => '/^(?:401178|636368|438935|504175|451416|636297|401179|431274|438935|451416|457393|457631|457632|504175|627780|636297|636368|
                            655000|655001|651652|651653|651654|650485|650486|650487|650488|506699|5067[0-6][0-9]|
                            50677[0-8]|509\d{3})\d{10}\d{0,10}|(((5067)|(4576)|(4011))\d{0,12})$/',
                'amex'      => '/^3[47]\d{13}$/',
                'jcb'       => '/^(?:2131|1800|35\d{3})\d{11}$/',
                'aura'      => '/^(5078\d{2})(\d{2})(\d{11})$/',
                'hipercard' => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
                'maestro'   => '/^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/',
            ];

            foreach ($brands as $brand => $regex) {
                if (preg_match($regex, $cardNumber)) {
                    $cardBrand = $brand;
                    break;
                }
            }

            dd($cardBrand);

            return $cardBrand;
            $notazzIntegration = new NotazzIntegration();
            $integrations      = $notazzIntegration->with([
                                                              'project',
                                                              'user',
                                                          ])
                                                   ->whereNotNull('start_date')
                                                   ->whereNull('retroactive_generated_date')
                                                   ->get();
            dd($integrations);
            $user = User::find(45);

            $user->loadMissing(["userNotification"]);
            $userNotification = $user->userNotification ?? null;
            dd($userNotification->released_balance ?? true);
            if ($userNotification->released_balance ?? true) {
                dd('opa');
            }
            dd('saiu');

            User::create(
                [
                    'name'                                => "Teste Maciel",
                    'email'                               => "testemaciel" . random_int(10, 999999999) . "@gmail.com",
                    'email_verified'                      => "1",
                    'password'                            => bcrypt(123456789),
                    'remember_token'                      => "",
                    'cellphone'                           => "24999309321",
                    'cellphone_verified'                  => "1",
                    'document'                            => "",
                    'zip_code'                            => "27521130",
                    'country'                             => "BR",
                    'state'                               => "Rio de Janeiro",
                    'city'                                => "Resende",
                    'neighborhood'                        => "",
                    'street'                              => "",
                    'number'                              => "",
                    'complement'                          => "",
                    'photo'                               => "",
                    'date_birth'                          => "1997-08-14",
                    'address_document_status'             => "3",
                    'personal_document_status'            => "3",
                    'score'                               => "",
                    'sms_zenvia_amount'                   => "1",
                    'percentage_rate'                     => "",
                    'transaction_rate'                    => "",
                    'foxcoin'                             => "",
                    'email_amount'                        => "",
                    'call_amount'                         => "",
                    'boleto_antecipation_money_days'      => "1",
                    'credit_card_antecipation_money_days' => "1",
                    'release_money_days'                  => "1",
                    'percentage_antecipable'              => "1",
                    'antecipation_tax'                    => "1",
                    'invites_amount'                      => "1",
                    'installment_tax'                     => "1",
                    'credit_card_release_money_days'      => "1",
                    'debit_card_release_money_days'       => "1",
                    'boleto_release_money_days'           => "1",
                    'boleto_tax'                          => "1",
                    'credit_card_tax'                     => "1",
                    'debit_card_tax'                      => "1",
                ]
            );
            $user = auth()->user();
            $user->load(["userNotification"]);

            dd($user);
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    /**
     * @param Request $request
     */
    public function heroFunction(Request $request)
    {
        $tes             = 0;
        $data            = [
            'name'        => 'Hero Produtor',
            'transaction' => 'nao tem', // code da venda
            'date'        => Carbon::now()->format('d/m/Y H:i:s'), //data da aprovacao
        ];
        $sendGridService = new SendgridService();
        $sendGridService->sendEmail('noreply', 'Cloudfox', 'emailteste@gmail.com', 'Hero Produtor', 'd-d65e83a8aa7e44c19b13d8b1cce0176c', $data);
    }

    /**
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function documentStatus()
    {
        $userDocumentModel = new UserDocument();
        $userModel         = new User();
        $companyDocument   = new CompanyDocument();
        $companyModel      = new Company();

        //Verifica os documentos aprovados do usuario
        $userAddressDocuments = $userDocumentModel->where('status', 3)
                                                  ->where('document_type_enum', 2)
                                                  ->with('user')
                                                  ->get();

        $count = 0;

        foreach ($userAddressDocuments as $userAddressDocument) {

            if ($userAddressDocument->user->address_document_status != 3) {
                $count++;
            }
        }

        dd($count);
    }
}


