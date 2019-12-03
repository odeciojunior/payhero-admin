<?php

namespace App\Http\Controllers\Dev;

use App\Jobs\SendNotazzInvoiceJob;
use Exception;
use Modules\Core\Entities\CompanyDocument;
use Modules\Core\Entities\NotazzIntegration;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Entities\NotazzSentHistory;
use Modules\Core\Entities\Pixel;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\SentEmail;
use Modules\Core\Entities\UserDocument;
use Modules\Core\Entities\UserNotification;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\CurrencyQuotationService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\SendgridService;
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
    public function tgFunction()
    {

        //nada

        dd('nada');

        //        $notazzService = new NotazzService();
        //
        //
        //        $result = $notazzService->sendNfse(2622);

        //        $notazzInvoiceModel       = new NotazzInvoice();
        //        $notazzSentHistoryModel   = new NotazzSentHistory();
        //        $saleModel                = new Sale();
        //        $productPlanModel         = new ProductPlan();
        //        $currencyQuotationService = new CurrencyQuotationService();
        //        $notazzInvoice = $notazzInvoiceModel->with([
        //                                                       'sale',
        //                                                       'sale.client',
        //                                                       'sale.delivery',
        //                                                       'sale.shipping',
        //                                                       'sale.plansSales.plan.products',
        //                                                       'sale.project.notazzIntegration',
        //                                                   ])->find(8177);
        //
        //        $sale = $notazzInvoice->sale;
        //        if ($sale) {
        //            //venda encontrada
        //
        //            $sale = $saleModel->with(['plansSales'])->find($sale->id);
        //
        //            $productsSale = collect();
        //            /** @var PlanSale $planSale */
        //            foreach ($sale->plansSales as $planSale) {
        //                /** @var ProductPlan $productPlan */
        //                foreach ($planSale->plan->productsPlans as $productPlan) {
        //
        //                    $product = $productPlan->product()->first();
        //
        //                    if (!empty($productPlan->cost)) {
        //                        //pega os valores de productplan
        //                        $product['product_cost']       = preg_replace("/[^0-9]/", "", $productPlan->cost);
        //                        $product['product_cost']       = (is_numeric($product['product_cost'])) ? $product['product_cost'] : 0;
        //                        $product['currency_type_enum'] = $productPlan->currency_type_enum;
        //                    } else {
        //                        //pega os valores de produto
        //                        if (!empty($product->cost)) {
        //                            $product['product_cost'] = preg_replace("/[^0-9]/", "", $product->cost);
        //                            $product['product_cost'] = (is_numeric($product['product_cost'])) ? $product['product_cost'] : 0;
        //                        } else {
        //                            $product['product_cost'] = 0;
        //                        }
        //
        //                        $product['currency_type_enum'] = $product->currency_type_enum ?? 1;
        //                    }
        //
        //                    $product['product_amount'] = ($productPlan->amount * $planSale->amount) ?? 1;
        //
        //                    $productsSale->add($product);
        //                }
        //            }
        //
        //            $products = $productsSale;
        //
        //            if ($products) {
        //                $costTotal = 0;
        //                foreach ($products as $product) {
        //
        //                    if ($product['currency_type_enum'] == $productPlanModel->present()->getCurrency('USD')) {
        //                        //moeda USD
        //                        $lastUsdQuotation        = $currencyQuotationService->getLastUsdQuotation();
        //                        $product['product_cost'] = (int) ($product['product_cost'] * ($lastUsdQuotation->value / 100));
        //                    }
        //
        //                    $costTotal += (int) ($product['product_cost'] * $product['product_amount']);
        //                }
        //
        //                $shippingCost = preg_replace("/[^0-9]/", "", $sale->shipment_value);
        //
        //                $subTotal  = preg_replace("/[^0-9]/", "", $sale->sub_total);
        //                $baseValue = ($subTotal + $shippingCost) - $costTotal;
        //
        //                $totalValue = substr_replace($baseValue, '.', strlen($baseValue) - 2, 0);
        //
        //                if ($totalValue <= 0) {
        //                    $totalValue = 1;
        //                }
        //
        //                $tokenApi = $sale->project->notazzIntegration->token_api;
        //
        //                $pendingDays = $sale->project->notazzIntegration->pending_days ?? 1;
        //            }
        //        }

        //---------------------------------------------------------------------------
        //        $notazInvoiceModel = new NotazzInvoice();
        //        $nservice          = new NotazzService();
        //
        //        $invoices = $notazInvoiceModel->whereIn('notazz_integration_id', [4, 5, 6])
        //                                      ->where('status', '!=', 5)
        //                                      ->get();
        //
        //        try {
        //            $count = 0;
        //            foreach ($invoices as $invoice) {
        //                if ($count > 90) {
        //                    break;
        //                }
        //                $ret = $nservice->deleteNfse($invoice->id);
        //                if ($ret == false) {
        //                    $invoice->update([
        //                                         'status'           => 5,
        //                                     ]);
        //                    continue;
        //                }
        //
        //                $invoice->update([
        //                                     'status'           => 5,
        //                                     'return_message'   => $ret->statusProcessamento,
        //                                     'return_http_code' => $ret->codigoProcessamento,
        //                                 ]);
        //
        //                $count = $count + 1;
        //            }
        //
        //            dd('ok');
        //        } catch (Exception $ex) {
        //            dd($ex);
        //        }
        //
        //        dd($invoices);
        //---------------------------------------------------------------------------

        //$ret = $nservice->deleteNfse(123);
        //dd($ret);

        /*----------------------------------------------------------------------------
                $notazInvoiceModel = new NotazzInvoice();
                $nservice          = new NotazzService();

                $invoices = $notazInvoiceModel->where('status','!=',5)->get();

                //        $ret = $nservice->consultNfse(2622);
                //        dd($ret);

                try {
                    $count = 0;
                    foreach ($invoices as $invoice) {
                        if($count > 90)
                        {
                            break;
                        }
                        $ret = $nservice->deleteNfse($invoice->id);
                        if($ret == false)
                        {
                            continue;
                        }

                        $invoice->update([
                                             'status'   => 5,
                                             'return_message'   => $ret->statusProcessamento,
                                             'return_http_code' => $ret->codigoProcessamento,
                                         ]);
                        $count = $count + 1;
                    }

                    dd('ok');

                } catch (Exception $ex) {
                    dd($ex);
                }

                dd('fim');

                -------------------------------------------------------------------------------*/

        //$nservice->createInvoice(4,3533,1 );
        //dd($nservice->consultNfse('1afe5fa65bf25a023df701e99a64962f'));

        /*



                $nservice  = new NotazzService();
                $saleModel = new Sale();

                $sale = $saleModel->with(['project', 'project.notazzIntegration'])->find(7437);

                SendNotazzInvoiceJob::dispatch(9)->delay(rand(1, 3));

                //$nservice->createInvoice($sale->project->notazzIntegration->id, $sale->id, 1);

                dd('aaa');

                $quotationService = new CurrencyQuotationService();

                dd($quotationService->getLastUsdQuotation());

                $nservice = new NotazzService();
                dd($nservice->consultNfse(6));

                $shopifyService = new ShopifyService('chegou-brasil.myshopify.com', '89ce66f4c04be336bbe09efdf1093b50');

                $shopifyService->setThemeByRole('main');
                $htmlCart = $shopifyService->getTemplateHtml('sections/product-template.liquid');

                dd($htmlCart);

                $html = "<form method='post' action='/cart/add' id='product_form_4299095048252' accept-charset='UTF-8' class='product-form product-form-product-template
        ' enctype='multipart/form-data' novalidate='novalidate' data-product-form=''><input type='hidden' name='form_type' value='product'><input type='hidden' name='utf8' value='✓'>


                        <div class='selector-wrapper js product-form__item'>
                          <label for='SingleOptionSelector-0'>
                            Size
                          </label>
                          <select class='single-option-selector single-option-selector-product-template product-form__input' id='SingleOptionSelector-0' data-index='option1'>

                              <option value='XXL' selected='selected'>XXL</option>

                              <option value='L'>L</option>

                              <option value='XL'>XL</option>

                              <option value='S'>S</option>

                              <option value='M'>M</option>

                          </select>
                        </div>

                        <div class='selector-wrapper js product-form__item'>
                          <label for='SingleOptionSelector-1'>
                            Source
                          </label>
                          <select class='single-option-selector single-option-selector-product-template product-form__input' id='SingleOptionSelector-1' data-index='option2'>

                              <option value='cosplay costume' selected='selected'>cosplay costume</option>

                          </select>
                        </div>



                    <select name='id' id='ProductSelect-product-template' class='product-form__variants no-js'>


                          <option selected='selected' value='30973042851900'>
                            XXL / cosplay costume
                          </option>



                          <option value='30973042917436'>
                            L / cosplay costume
                          </option>



                          <option value='30973042982972'>
                            XL / cosplay costume
                          </option>



                          <option value='30973043048508'>
                            S / cosplay costume
                          </option>



                          <option value='30973043114044'>
                            M / cosplay costume
                          </option>


                    </select>



                    <div class='product-form__error-message-wrapper product-form__error-message-wrapper--hidden product-form__error-message-wrapper--has-payment-button' data-error-message-wrapper='' role='alert'>
                      <span class='visually-hidden'>Error </span>
                      <svg aria-hidden='true' focusable='false' role='presentation' class='icon icon-error' viewBox='0 0 14 14'><g fill='none' fill-rule='evenodd'><path d='M7 0a7 7 0 0 1 7 7 7 7 0 1 1-7-7z'></path><path class='icon-error__symbol' d='M6.328 8.396l-.252-5.4h1.836l-.24 5.4H6.328zM6.04 10.16c0-.528.432-.972.96-.972s.972.444.972.972c0 .516-.444.96-.972.96a.97.97 0 0 1-.96-.96z' fill-rule='nonzero'></path></g></svg>
                      <span class='product-form__error-message' data-error-message=''>Quantity must be 1 or more</span>
                    </div>

                    <div class='product-form__item product-form__item--submit product-form__item--payment-button'>
                      <button type='submit' name='add' aria-label='Add to cart' class='btn product-form__cart-submit btn--secondary-accent' data-add-to-cart=''>
                        <span data-add-to-cart-text=''>

                            Add to cart

                        </span>
                        <span class='hide' data-loader=''>
                          <svg aria-hidden='true' focusable='false' role='presentation' class='icon icon-spinner' viewBox='0 0 20 20'><path d='M7.229 1.173a9.25 9.25 0 1 0 11.655 11.412 1.25 1.25 0 1 0-2.4-.698 6.75 6.75 0 1 1-8.506-8.329 1.25 1.25 0 1 0-.75-2.385z' fill='#919EAB'></path></svg>
                        </span>
                      </button>

                        <div data-shopify='payment-button' class='shopify-payment-button'><div><div><div><div class='shopify-cleanslate'><div id='shopify-svg-symbols' class='VoW3UuJKYxZJHMpUkDNUv' aria-hidden='true'><svg xmlns='http://www.w3.org/2000/svg' xmlnsXlink='http://www.w3.org/1999/xlink' focusable='false'><defs><symbol id='shopify-svg__warning' viewBox='0 0 16 14'><path d='M5.925 2.344c1.146-1.889 3.002-1.893 4.149 0l4.994 8.235c1.146 1.889.288 3.421-1.916 3.421h-10.305c-2.204 0-3.063-1.529-1.916-3.421l4.994-8.235zm1.075 1.656v5h2v-5h-2zm0 6v2h2v-2h-2z'></path></symbol><symbol id='shopify-svg__loading' viewBox='0 0 32 32'><path d='M32 16c0 8.837-7.163 16-16 16S0 24.837 0 16 7.163 0 16 0v2C8.268 2 2 8.268 2 16s6.268 14 14 14 14-6.268 14-14h2z'></path></symbol><symbol id='shopify-svg__error' viewBox='0 0 18 18'><path fill='#FF3E3E' d='M9 18c5 0 9-4 9-9s-4-9-9-9-9 4-9 9 4 9 9 9z'></path><path fill='#FFFFFF' d='M8 4h2v6H8z'></path><rect fill='#FFFFFF' x='7.8' y='12' width='2.5' height='2.5' rx='1.3'></rect></symbol><symbol id='shopify-svg__close-circle' viewBox='0 0 16 16'><circle cx='8' cy='8' r='8'></circle><path d='M10.5 5.5l-5 5M5.5 5.5l5 5' stroke='#FFF' stroke-width='1.5' stroke-linecap='square'></path></symbol><symbol id='shopify-svg__close' viewBox='0 0 20 20'><path d='M17.1 4.3l-1.4-1.4-5.7 5.7-5.7-5.7-1.4 1.4 5.7 5.7-5.7 5.7 1.4 1.4 5.7-5.7 5.7 5.7 1.4-1.4-5.7-5.7z'></path></symbol><symbol id='shopify-svg__arrow-right' viewBox='0 0 16 16'><path d='M16 8.1l-8.1 8.1-1.1-1.1L13 8.9H0V7.3h13L6.8 1.1 7.9 0 16 8.1z'></path></symbol><symbol id='shopify-svg__payments-google-pay-light' viewBox='0 0 41 17'><path fill='rgba(255, 255, 255, 1)' d='M19.526 2.635v4.083h2.518c.6 0 1.096-.202 1.488-.605.403-.402.605-.882.605-1.437 0-.544-.202-1.018-.605-1.422-.392-.413-.888-.62-1.488-.62h-2.518zm0 5.52v4.736h-1.504V1.198h3.99c1.013 0 1.873.337 2.582 1.012.72.675 1.08 1.497 1.08 2.466 0 .991-.36 1.819-1.08 2.482-.697.665-1.559.996-2.583.996h-2.485v.001zM27.194 10.442c0 .392.166.718.499.98.332.26.722.391 1.168.391.633 0 1.196-.234 1.692-.701.497-.469.744-1.019.744-1.65-.469-.37-1.123-.555-1.962-.555-.61 0-1.12.148-1.528.442-.409.294-.613.657-.613 1.093m1.946-5.815c1.112 0 1.989.297 2.633.89.642.594.964 1.408.964 2.442v4.932h-1.439v-1.11h-.065c-.622.914-1.45 1.372-2.486 1.372-.882 0-1.621-.262-2.215-.784-.594-.523-.891-1.176-.891-1.96 0-.828.313-1.486.94-1.976s1.463-.735 2.51-.735c.892 0 1.629.163 2.206.49v-.344c0-.522-.207-.966-.621-1.33a2.132 2.132 0 0 0-1.455-.547c-.84 0-1.504.353-1.995 1.062l-1.324-.834c.73-1.045 1.81-1.568 3.238-1.568M40.993 4.889l-5.02 11.53H34.42l1.864-4.034-3.302-7.496h1.635l2.387 5.749h.032l2.322-5.75z'></path><path fill='#4285F4' d='M13.448 7.134c0-.473-.04-.93-.116-1.366H6.988v2.588h3.634a3.11 3.11 0 0 1-1.344 2.042v1.68h2.169c1.27-1.17 2.001-2.9 2.001-4.944'></path><path fill='#34A853' d='M6.988 13.7c1.816 0 3.344-.595 4.459-1.621l-2.169-1.681c-.603.406-1.38.643-2.29.643-1.754 0-3.244-1.182-3.776-2.774H.978v1.731a6.728 6.728 0 0 0 6.01 3.703'></path><path fill='#FBBC05' d='M3.212 8.267a4.034 4.034 0 0 1 0-2.572V3.964H.978A6.678 6.678 0 0 0 .261 6.98c0 1.085.26 2.11.717 3.017l2.234-1.731z'></path><path fill='#EA4335' d='M6.988 2.921c.992 0 1.88.34 2.58 1.008v.001l1.92-1.918C10.324.928 8.804.262 6.989.262a6.728 6.728 0 0 0-6.01 3.702l2.234 1.731c.532-1.592 2.022-2.774 3.776-2.774'></path></symbol><symbol id='shopify-svg__payments-google-pay-dark' viewBox='0 0 41 17'><path fill='rgba(0, 0, 0, .55)' d='M19.526 2.635v4.083h2.518c.6 0 1.096-.202 1.488-.605.403-.402.605-.882.605-1.437 0-.544-.202-1.018-.605-1.422-.392-.413-.888-.62-1.488-.62h-2.518zm0 5.52v4.736h-1.504V1.198h3.99c1.013 0 1.873.337 2.582 1.012.72.675 1.08 1.497 1.08 2.466 0 .991-.36 1.819-1.08 2.482-.697.665-1.559.996-2.583.996h-2.485v.001zM27.194 10.442c0 .392.166.718.499.98.332.26.722.391 1.168.391.633 0 1.196-.234 1.692-.701.497-.469.744-1.019.744-1.65-.469-.37-1.123-.555-1.962-.555-.61 0-1.12.148-1.528.442-.409.294-.613.657-.613 1.093m1.946-5.815c1.112 0 1.989.297 2.633.89.642.594.964 1.408.964 2.442v4.932h-1.439v-1.11h-.065c-.622.914-1.45 1.372-2.486 1.372-.882 0-1.621-.262-2.215-.784-.594-.523-.891-1.176-.891-1.96 0-.828.313-1.486.94-1.976s1.463-.735 2.51-.735c.892 0 1.629.163 2.206.49v-.344c0-.522-.207-.966-.621-1.33a2.132 2.132 0 0 0-1.455-.547c-.84 0-1.504.353-1.995 1.062l-1.324-.834c.73-1.045 1.81-1.568 3.238-1.568M40.993 4.889l-5.02 11.53H34.42l1.864-4.034-3.302-7.496h1.635l2.387 5.749h.032l2.322-5.75z'></path><path fill='#4285F4' d='M13.448 7.134c0-.473-.04-.93-.116-1.366H6.988v2.588h3.634a3.11 3.11 0 0 1-1.344 2.042v1.68h2.169c1.27-1.17 2.001-2.9 2.001-4.944'></path><path fill='#34A853' d='M6.988 13.7c1.816 0 3.344-.595 4.459-1.621l-2.169-1.681c-.603.406-1.38.643-2.29.643-1.754 0-3.244-1.182-3.776-2.774H.978v1.731a6.728 6.728 0 0 0 6.01 3.703'></path><path fill='#FBBC05' d='M3.212 8.267a4.034 4.034 0 0 1 0-2.572V3.964H.978A6.678 6.678 0 0 0 .261 6.98c0 1.085.26 2.11.717 3.017l2.234-1.731z'></path><path fill='#EA4335' d='M6.988 2.921c.992 0 1.88.34 2.58 1.008v.001l1.92-1.918C10.324.928 8.804.262 6.989.262a6.728 6.728 0 0 0-6.01 3.702l2.234 1.731c.532-1.592 2.022-2.774 3.776-2.774'></path></symbol><symbol id='shopify-svg__payments-amazon-pay' viewBox='0 0 102 20'><path fill='#333e48' d='M75.19 1.786c-.994 0-1.933.326-2.815.98v5.94c.896.683 1.82 1.023 2.774 1.023 1.932 0 2.899-1.32 2.899-3.96 0-2.655-.953-3.983-2.858-3.983zm-2.962-.277A5.885 5.885 0 0 1 73.93.444a4.926 4.926 0 0 1 1.85-.362c.672 0 1.282.127 1.827.383a3.763 3.763 0 0 1 1.387 1.108c.378.482.669 1.068.872 1.757.203.689.305 1.466.305 2.332 0 .88-.109 1.675-.326 2.385-.217.71-.522 1.314-.914 1.81a4.137 4.137 0 0 1-1.429 1.16 4.165 4.165 0 0 1-1.87.416c-1.26 0-2.346-.419-3.256-1.256v4.983c0 .284-.14.426-.42.426h-1.24c-.28 0-.42-.142-.42-.426V.827c0-.284.14-.426.42-.426h.925c.28 0 .441.142.483.426l.105.682zm13.194 8.37a4.21 4.21 0 0 0 1.45-.277 5.463 5.463 0 0 0 1.45-.81V6.62c-.35-.085-.719-.152-1.104-.202a8.8 8.8 0 0 0-1.124-.075c-1.583 0-2.374.617-2.374 1.853 0 .54.147.955.441 1.246.294.29.715.437 1.261.437zm-2.458-7.625l-.158.053a.561.561 0 0 1-.179.033c-.182 0-.273-.128-.273-.384V1.38c0-.199.028-.337.084-.415.056-.078.169-.153.337-.224.448-.199 1-.359 1.66-.48.657-.12 1.316-.18 1.974-.18 1.33 0 2.311.277 2.942.83.63.554.945 1.413.945 2.577v7.284c0 .284-.14.426-.42.426h-.903c-.267 0-.42-.135-.463-.405l-.105-.702a5.74 5.74 0 0 1-1.67 1.022 4.908 4.908 0 0 1-1.817.362c-1.009 0-1.807-.288-2.395-.863-.589-.575-.883-1.345-.883-2.31 0-1.037.364-1.864 1.092-2.481.73-.618 1.71-.927 2.942-.927.784 0 1.667.12 2.647.362V3.852c0-.767-.168-1.307-.504-1.619-.336-.313-.925-.469-1.764-.469-.982 0-2.01.163-3.09.49zm14.16 10.84c-.379.98-.816 1.683-1.314 2.109-.496.426-1.144.639-1.943.639-.448 0-.847-.05-1.197-.15a.606.606 0 0 1-.336-.202c-.07-.093-.105-.237-.105-.437V14.5c0-.27.105-.405.315-.405.07 0 .175.014.315.043.14.028.33.043.567.043.532 0 .946-.128 1.24-.384.294-.255.56-.724.798-1.406l.4-1.086-4.056-10.137c-.098-.241-.146-.411-.146-.511 0-.17.097-.256.294-.256h1.26c.224 0 .378.036.463.106.083.072.167.228.251.47l2.942 8.263L99.708.976c.084-.24.168-.397.252-.469.084-.07.238-.106.462-.106h1.177c.196 0 .294.086.294.256 0 .1-.05.27-.147.51l-4.622 11.927M40.15 15.47c-3.761 2.814-9.216 4.31-13.912 4.31-6.583 0-12.51-2.466-16.996-6.572-.352-.322-.038-.763.385-.513 4.84 2.855 10.825 4.574 17.006 4.574 4.17 0 8.753-.877 12.971-2.691.636-.273 1.17.425.547.891'></path><path fill='#333e48' d='M41.717 13.657c-.482-.624-3.181-.296-4.394-.148-.368.044-.425-.281-.093-.517 2.153-1.533 5.682-1.09 6.092-.577.413.518-.108 4.104-2.127 5.816-.31.263-.605.122-.468-.225.455-1.15 1.471-3.724.99-4.349M37.429 2.06V.57A.365.365 0 0 1 37.8.193l6.59-.001c.21 0 .38.155.38.376v1.278c-.003.214-.18.494-.496.938L40.86 7.722c1.267-.03 2.607.163 3.757.818.26.148.33.367.35.582v1.59c0 .218-.237.472-.485.34-2.028-1.077-4.718-1.194-6.96.013-.23.124-.47-.126-.47-.345V9.209c0-.242.005-.656.246-1.024l3.953-5.75H37.81a.369.369 0 0 1-.38-.375M13.4 11.365h-2.005a.38.38 0 0 1-.358-.343L11.038.595a.38.38 0 0 1 .387-.375h1.866a.38.38 0 0 1 .365.35v1.36h.037C14.18.615 15.096 0 16.331 0c1.253 0 2.039.614 2.6 1.93C19.418.615 20.521 0 21.7 0c.842 0 1.758.351 2.32 1.141.635.878.505 2.15.505 3.27l-.002 6.58a.38.38 0 0 1-.387.374h-2.001a.378.378 0 0 1-.36-.374V5.463c0-.438.037-1.535-.056-1.952-.15-.703-.6-.9-1.179-.9-.486 0-.991.33-1.197.855-.206.527-.188 1.405-.188 1.997v5.527a.38.38 0 0 1-.386.375h-2.002a.379.379 0 0 1-.36-.374l-.001-5.528c0-1.163.186-2.874-1.235-2.874-1.44 0-1.384 1.668-1.384 2.874l-.001 5.527a.38.38 0 0 1-.387.375m37.059-9.236c-1.478 0-1.571 2.04-1.571 3.312 0 1.273-.02 3.993 1.552 3.993 1.554 0 1.628-2.194 1.628-3.532 0-.877-.038-1.93-.3-2.764-.224-.724-.673-1.01-1.31-1.01zM50.439 0c2.975 0 4.584 2.59 4.584 5.88 0 3.181-1.777 5.705-4.584 5.705-2.918 0-4.508-2.59-4.508-5.814C45.93 2.523 47.539 0 50.439 0zm8.441 11.365h-1.997a.379.379 0 0 1-.36-.374L56.52.561a.381.381 0 0 1 .386-.34L58.764.22c.175.009.32.13.356.291v1.595h.038C59.72.68 60.505 0 61.89 0c.898 0 1.778.329 2.339 1.229.524.834.524 2.237.524 3.247v6.561a.382.382 0 0 1-.385.328H62.36a.38.38 0 0 1-.357-.328V5.376c0-1.141.13-2.809-1.253-2.809-.487 0-.936.33-1.16.834-.281.636-.319 1.272-.319 1.975v5.614a.386.386 0 0 1-.39.375m-24.684.075a.41.41 0 0 1-.473.047c-.665-.56-.785-.82-1.149-1.354-1.1 1.136-1.879 1.477-3.304 1.477-1.687 0-3-1.055-3-3.166 0-1.65.882-2.77 2.138-3.32 1.087-.484 2.606-.572 3.769-.704v-.264c0-.484.037-1.055-.245-1.473-.243-.374-.712-.528-1.124-.528-.765 0-1.444.397-1.611 1.22-.035.183-.167.364-.348.374l-1.943-.214c-.164-.037-.346-.17-.299-.425C27.055.721 29.183 0 31.09 0c.975 0 2.25.263 3.018 1.011.975.924.881 2.155.881 3.497v3.165c0 .952.39 1.37.757 1.882.128.185.156.405-.007.54-.409.348-1.136.988-1.537 1.35l-.005-.005zm-2.02-4.953v-.44c-1.45 0-2.98.314-2.98 2.045 0 .88.45 1.473 1.218 1.473.562 0 1.069-.352 1.387-.923.394-.704.376-1.363.376-2.155zM7.926 11.44a.41.41 0 0 1-.473.047c-.667-.56-.786-.82-1.15-1.354C5.204 11.27 4.425 11.61 3 11.61c-1.688 0-3-1.055-3-3.166 0-1.65.88-2.77 2.137-3.32 1.087-.484 2.606-.572 3.768-.704v-.264c0-.484.038-1.055-.243-1.473-.244-.374-.713-.528-1.125-.528-.764 0-1.444.397-1.61 1.22-.036.183-.168.364-.35.374l-1.94-.214c-.165-.037-.347-.17-.3-.425C.783.721 2.911 0 4.818 0c.975 0 2.25.263 3.018 1.011.975.924.882 2.155.882 3.497v3.165c0 .952.39 1.37.756 1.882.128.185.157.405-.006.54a78.47 78.47 0 0 0-1.537 1.35l-.005-.005zm-2.02-4.953v-.44c-1.45 0-2.982.314-2.982 2.045 0 .88.45 1.473 1.219 1.473.562 0 1.069-.352 1.387-.923.394-.704.375-1.363.375-2.155z'></path></symbol><symbol id='shopify-svg__payments-apple-pay-dark' viewBox='0 0 43 19'><path fill='#000000' d='M6.948 1.409C7.934.147 9.305.147 9.305.147s.193 1.18-.771 2.316c-1.05 1.2-2.228.993-2.228.993s-.236-.93.642-2.047zM3.82 3.663c-1.735 0-3.6 1.51-3.6 4.363 0 2.916 2.186 6.555 3.943 6.555.6 0 1.543-.6 2.485-.6.922 0 1.607.559 2.464.559 1.907 0 3.322-3.826 3.322-3.826s-2.015-.744-2.015-2.936c0-1.944 1.629-2.73 1.629-2.73s-.836-1.447-2.936-1.447c-1.22 0-2.164.661-2.656.661-.622.021-1.5-.6-2.636-.6zM19.64 1.426c2.453 0 4.188 1.788 4.188 4.396 0 2.608-1.755 4.417-4.248 4.417h-2.932v4.564h-1.974V1.426h4.966zm-2.992 7.067h2.473c1.695 0 2.693-.967 2.693-2.65 0-1.683-.978-2.671-2.693-2.671h-2.473v5.321zm7.559 3.429c0-1.767 1.296-2.777 3.65-2.945l2.572-.147v-.78c0-1.156-.738-1.787-1.994-1.787-1.037 0-1.795.568-1.955 1.43h-1.775c.06-1.788 1.656-3.092 3.79-3.092 2.333 0 3.829 1.304 3.829 3.281v6.9h-1.815v-1.684h-.04c-.519 1.094-1.715 1.788-3.012 1.788-1.934.021-3.25-1.178-3.25-2.965zm6.222-.905v-.778l-2.313.168c-1.297.084-1.975.59-1.975 1.494 0 .862.718 1.409 1.815 1.409 1.396-.021 2.473-.968 2.473-2.293zm3.969 7.383v-1.64c.14.041.438.041.598.041.897 0 1.416-.4 1.735-1.472l.14-.526L33.4 4.707h2.054l2.453 8.224h.04L40.4 4.707h1.994l-3.57 10.538c-.818 2.419-1.715 3.197-3.67 3.197-.14.02-.598-.021-.757-.042z'></path></symbol><symbol id='shopify-svg__payments-apple-pay-light' viewBox='0 0 43 19'><path fill='#FFFFFF' d='M6.948 1.409C7.934.147 9.305.147 9.305.147s.193 1.18-.771 2.316c-1.05 1.2-2.228.993-2.228.993s-.236-.93.642-2.047zM3.82 3.663c-1.735 0-3.6 1.51-3.6 4.363 0 2.916 2.186 6.555 3.943 6.555.6 0 1.543-.6 2.485-.6.922 0 1.607.559 2.464.559 1.907 0 3.322-3.826 3.322-3.826s-2.015-.744-2.015-2.936c0-1.944 1.629-2.73 1.629-2.73s-.836-1.447-2.936-1.447c-1.22 0-2.164.661-2.656.661-.622.021-1.5-.6-2.636-.6zM19.64 1.426c2.453 0 4.188 1.788 4.188 4.396 0 2.608-1.755 4.417-4.248 4.417h-2.932v4.564h-1.974V1.426h4.966zm-2.992 7.067h2.473c1.695 0 2.693-.967 2.693-2.65 0-1.683-.978-2.671-2.693-2.671h-2.473v5.321zm7.559 3.429c0-1.767 1.296-2.777 3.65-2.945l2.572-.147v-.78c0-1.156-.738-1.787-1.994-1.787-1.037 0-1.795.568-1.955 1.43h-1.775c.06-1.788 1.656-3.092 3.79-3.092 2.333 0 3.829 1.304 3.829 3.281v6.9h-1.815v-1.684h-.04c-.519 1.094-1.715 1.788-3.012 1.788-1.934.021-3.25-1.178-3.25-2.965zm6.222-.905v-.778l-2.313.168c-1.297.084-1.975.59-1.975 1.494 0 .862.718 1.409 1.815 1.409 1.396-.021 2.473-.968 2.473-2.293zm3.969 7.383v-1.64c.14.041.438.041.598.041.897 0 1.416-.4 1.735-1.472l.14-.526L33.4 4.707h2.054l2.453 8.224h.04L40.4 4.707h1.994l-3.57 10.538c-.818 2.419-1.715 3.197-3.67 3.197-.14.02-.598-.021-.757-.042z'></path></symbol><symbol id='shopify-svg__payments-paypal' viewBox='0 0 67 19'><path fill='#253b80' d='M8.44.57H3.29a.718.718 0 0 0-.707.61L.502 14.517c-.041.263.16.5.425.5h2.458a.718.718 0 0 0 .707-.61l.561-3.597a.717.717 0 0 1 .706-.611h1.63c3.391 0 5.349-1.658 5.86-4.944.23-1.437.01-2.566-.657-3.357C11.461 1.029 10.162.57 8.44.57zm.594 4.87C8.752 7.308 7.34 7.308 5.976 7.308h-.777l.545-3.485a.43.43 0 0 1 .424-.366h.356c.93 0 1.807 0 2.26.535.27.32.353.794.25 1.45zm14.796-.06h-2.466a.43.43 0 0 0-.424.367l-.109.696-.172-.252c-.534-.783-1.724-1.044-2.912-1.044-2.725 0-5.052 2.084-5.505 5.008-.235 1.46.1 2.854.919 3.827.75.894 1.826 1.267 3.105 1.267 2.195 0 3.412-1.426 3.412-1.426l-.11.692a.432.432 0 0 0 .424.502h2.22a.718.718 0 0 0 .707-.61l1.333-8.526a.43.43 0 0 0-.423-.5zm-3.437 4.849c-.238 1.422-1.356 2.378-2.782 2.378-.716 0-1.288-.232-1.655-.672-.365-.436-.503-1.058-.387-1.75.222-1.41 1.359-2.397 2.763-2.397.7 0 1.269.235 1.644.678.375.448.524 1.073.417 1.763zM36.96 5.38h-2.478a.716.716 0 0 0-.592.318l-3.417 5.085-1.448-4.887a.719.719 0 0 0-.687-.515h-2.435a.433.433 0 0 0-.407.573l2.73 8.09-2.567 3.66a.434.434 0 0 0 .35.684h2.475a.712.712 0 0 0 .588-.31l8.24-12.016a.434.434 0 0 0-.352-.681z'></path><path fill='#179bd7' d='M45.163.57h-5.15a.717.717 0 0 0-.706.61l-2.082 13.337a.43.43 0 0 0 .423.5h2.642a.502.502 0 0 0 .494-.427l.591-3.78a.717.717 0 0 1 .706-.611h1.63c3.392 0 5.348-1.658 5.86-4.944.231-1.437.009-2.566-.657-3.357C48.183 1.029 46.886.57 45.163.57zm.593 4.87c-.28 1.867-1.692 1.867-3.057 1.867h-.777l.546-3.485a.429.429 0 0 1 .423-.366h.356c.93 0 1.807 0 2.26.535.27.32.353.794.25 1.45zm14.795-.06h-2.464a.428.428 0 0 0-.423.367l-.109.696-.173-.252c-.534-.783-1.723-1.044-2.911-1.044-2.724 0-5.05 2.084-5.504 5.008-.235 1.46.099 2.854.918 3.827.753.894 1.826 1.267 3.105 1.267 2.195 0 3.413-1.426 3.413-1.426l-.11.692a.432.432 0 0 0 .424.502h2.22a.717.717 0 0 0 .707-.61l1.333-8.526a.433.433 0 0 0-.426-.5zm-3.436 4.849c-.237 1.422-1.356 2.378-2.782 2.378-.714 0-1.288-.232-1.655-.672-.365-.436-.502-1.058-.387-1.75.223-1.41 1.359-2.397 2.763-2.397.7 0 1.269.235 1.644.678.377.448.526 1.073.417 1.763zM63.458.935l-2.113 13.582a.43.43 0 0 0 .423.5h2.124a.716.716 0 0 0 .707-.61L66.683 1.07a.432.432 0 0 0-.423-.5h-2.379c-.21 0-.39.156-.423.366z'></path></symbol><symbol id='shopify-svg__payments-shopify-pay-dark' viewBox='0 0 264 115'><path fill-rule='evenodd' clip-rule='evenodd' d='M129.553 53.8275C130.909 54.0355 132.266 54.2445 134.354 54.2445C143.538 54.2445 148.548 47.3565 148.548 40.5715C148.548 34.4135 144.478 31.9095 138.842 31.9095C136.546 31.9095 134.772 32.0135 133.623 32.3265L129.553 53.8275ZM121.516 21.9935C125.9 20.9495 132.476 20.3245 138.529 20.3245C144.374 20.3245 151.575 21.3675 156.481 25.2295C160.656 28.4645 163.056 33.4745 163.056 39.4245C163.056 48.5045 158.777 55.3925 153.662 59.5675C148.236 63.9515 140.407 65.9345 132.58 65.9345C130.597 65.9345 128.718 65.7255 127.362 65.6215L122.456 91.0885H108.366L121.516 21.9935Z' fill='black'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M194.628 50.2782C193.48 49.9662 192.228 49.8612 190.976 49.8612C181.582 49.8612 175.215 63.3252 175.215 72.1972C175.215 77.1022 176.78 80.4422 180.643 80.4422C184.922 80.4422 189.827 75.1192 192.02 63.5342L194.628 50.2782ZM190.14 91.0882C190.244 88.1662 190.558 84.9302 190.767 81.4862H190.453C185.966 89.4182 179.912 92.1322 174.589 92.1322C165.926 92.1322 160.395 85.2432 160.395 75.1192C160.395 58.4202 170.727 38.9022 194.941 38.9022C200.682 38.9022 206.735 40.0502 210.701 41.3022L205.274 68.5442C204.022 75.0142 203.082 84.9302 203.187 91.0882H190.14Z' fill='black'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M231.419 41.3724L233.715 63.0124C234.342 68.1264 234.759 71.7794 234.968 75.1194H235.177C236.325 71.6754 237.369 68.4394 239.456 62.9074L248.223 41.3724H263.253L245.614 77.8324C239.352 90.7754 233.299 100.272 226.723 106.431C221.608 111.232 215.555 113.632 212.633 114.259L208.667 102.047C211.066 101.212 214.094 99.9594 216.808 97.9764C220.148 95.6804 222.966 92.5494 224.635 89.3134C225.053 88.5834 225.157 87.9564 224.948 86.9134L216.963 41.3724H231.419Z' fill='black'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M40.3083 4.3368C40.8933 4.3498 41.4583 4.5408 41.9323 4.8838C37.8763 6.7928 33.5313 11.5948 31.6953 21.1858L23.9653 23.5788C26.1183 16.2638 31.2223 4.3368 40.3083 4.3368ZM44.0703 7.8938C45.0193 10.6298 45.4523 13.5188 45.3473 16.4128V16.9628L35.5713 19.9818C37.4543 12.7258 40.9833 9.2168 44.0683 7.8938H44.0703ZM53.4023 14.4708L48.6703 15.9358V14.9118C48.7243 12.3178 48.3403 9.7358 47.5403 7.2718C50.3373 7.6238 52.2073 10.8058 53.4003 14.4688L53.4023 14.4708ZM76.1213 20.0368C76.0533 19.6068 75.6993 19.2778 75.2643 19.2428C74.9093 19.2128 67.3683 18.6548 67.3683 18.6548C67.3683 18.6548 62.1283 13.4578 61.5553 12.8838C60.9283 12.4588 60.1373 12.3578 59.4233 12.6098L56.4933 13.5148C54.7443 8.4838 51.6573 3.8598 46.2263 3.8598C46.0763 3.8598 45.9213 3.8668 45.7673 3.8748C44.6183 2.1488 42.7243 1.0648 40.6553 0.945801C28.0003 0.945801 21.9593 16.7558 20.0643 24.7898C15.1493 26.3118 11.6593 27.3928 11.2103 27.5338C8.46731 28.3968 8.38131 28.4808 8.01631 31.0648C7.74731 33.0248 0.569305 88.5248 0.569305 88.5248L56.5163 99.0038L86.8313 92.4458C86.8313 92.4458 76.1883 20.5328 76.1213 20.0368Z' fill='#95BF46'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M75.2644 19.2426C74.9094 19.2126 67.3684 18.6556 67.3684 18.6556C67.3684 18.6556 62.1284 13.4596 61.5554 12.8836C61.3324 12.6776 61.0494 12.5476 60.7484 12.5126L56.5184 99.0106L86.8274 92.4556C86.8274 92.4556 76.1894 20.5326 76.1214 20.0386C76.0534 19.6076 75.7004 19.2786 75.2644 19.2426Z' fill='#5F8E3E'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M46.2263 35.9847L42.4883 47.1007C40.2173 45.9897 37.7303 45.3937 35.2033 45.3537C29.3243 45.3537 29.0213 49.0457 29.0213 49.9767C29.0213 55.0527 42.2583 57.0037 42.2583 68.8857C42.2583 78.2377 36.3243 84.2647 28.3203 84.2647C22.8563 84.4077 17.5843 82.2377 13.8043 78.2897L16.3763 69.7967C16.3763 69.7967 21.4253 74.1277 25.6853 74.1277C27.7573 74.2157 29.5093 72.6067 29.5983 70.5337C29.6013 70.4687 29.6023 70.4027 29.6013 70.3377C29.6013 63.7157 18.7373 63.4187 18.7373 52.5397C18.7373 43.3827 25.3113 34.5257 38.5843 34.5257C41.2123 34.3897 43.8333 34.8897 46.2263 35.9847Z' fill='white'></path></symbol><symbol id='shopify-svg__payments-shopify-pay-light' viewBox='0 0 264 115'><path fill-rule='evenodd' clip-rule='evenodd' d='M129.545 53.8275C130.902 54.0355 132.259 54.2445 134.346 54.2445C143.53 54.2445 148.54 47.3565 148.54 40.5715C148.54 34.4135 144.47 31.9095 138.834 31.9095C136.538 31.9095 134.764 32.0135 133.616 32.3265L129.545 53.8275ZM121.508 21.9935C125.892 20.9495 132.468 20.3245 138.521 20.3245C144.366 20.3245 151.567 21.3675 156.473 25.2295C160.648 28.4645 163.048 33.4745 163.048 39.4245C163.048 48.5045 158.769 55.3925 153.654 59.5675C148.228 63.9515 140.399 65.9345 132.572 65.9345C130.589 65.9345 128.71 65.7255 127.354 65.6215L122.448 91.0885H108.358L121.508 21.9935Z' fill='white'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M194.62 50.2782C193.472 49.9662 192.22 49.8612 190.968 49.8612C181.574 49.8612 175.207 63.3252 175.207 72.1972C175.207 77.1022 176.772 80.4422 180.635 80.4422C184.914 80.4422 189.819 75.1192 192.012 63.5342L194.62 50.2782ZM190.133 91.0882C190.237 88.1662 190.55 84.9302 190.759 81.4862H190.445C185.958 89.4182 179.904 92.1322 174.581 92.1322C165.918 92.1322 160.387 85.2432 160.387 75.1192C160.387 58.4202 170.72 38.9022 194.933 38.9022C200.674 38.9022 206.727 40.0502 210.693 41.3022L205.266 68.5442C204.014 75.0142 203.075 84.9302 203.18 91.0882H190.133Z' fill='white'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M231.412 41.3724L233.708 63.0124C234.335 68.1264 234.751 71.7794 234.96 75.1194H235.169C236.317 71.6754 237.361 68.4394 239.449 62.9074L248.216 41.3724H263.245L245.606 77.8324C239.345 90.7754 233.291 100.272 226.715 106.431C221.6 111.232 215.547 113.632 212.625 114.259L208.659 102.047C211.058 101.212 214.086 99.9594 216.8 97.9764C220.14 95.6804 222.958 92.5494 224.627 89.3134C225.046 88.5834 225.15 87.9564 224.941 86.9134L216.955 41.3724H231.412Z' fill='white'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M40.3005 4.3368C40.8855 4.3498 41.4505 4.5408 41.9245 4.8838C37.8685 6.7928 33.5235 11.5948 31.6885 21.1858L23.9575 23.5788C26.1115 16.2638 31.2145 4.3368 40.3005 4.3368ZM44.0625 7.8938C45.0115 10.6298 45.4445 13.5188 45.3395 16.4128V16.9628L35.5635 19.9818C37.4465 12.7258 40.9755 9.2168 44.0605 7.8938H44.0625ZM53.3945 14.4708L48.6625 15.9358V14.9118C48.7165 12.3178 48.3325 9.7358 47.5325 7.2718C50.3295 7.6238 52.1995 10.8058 53.3925 14.4688L53.3945 14.4708ZM76.1135 20.0368C76.0455 19.6068 75.6915 19.2778 75.2565 19.2428C74.9015 19.2128 67.3605 18.6548 67.3605 18.6548C67.3605 18.6548 62.1205 13.4578 61.5485 12.8838C60.9205 12.4588 60.1295 12.3578 59.4155 12.6098L56.4855 13.5148C54.7365 8.4838 51.6495 3.8598 46.2185 3.8598C46.0685 3.8598 45.9135 3.8668 45.7605 3.8748C44.6105 2.1488 42.7165 1.0648 40.6475 0.945801C27.9935 0.945801 21.9515 16.7558 20.0565 24.7898C15.1415 26.3118 11.6515 27.3928 11.2035 27.5338C8.45952 28.3968 8.37352 28.4808 8.00852 31.0648C7.73952 33.0248 0.561523 88.5248 0.561523 88.5248L56.5085 99.0038L86.8235 92.4458C86.8235 92.4458 76.1805 20.5328 76.1135 20.0368Z' fill='#95BF46'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M75.2566 19.2426C74.9016 19.2126 67.3606 18.6556 67.3606 18.6556C67.3606 18.6556 62.1206 13.4596 61.5486 12.8836C61.3246 12.6776 61.0426 12.5476 60.7406 12.5126L56.5106 99.0106L86.8196 92.4556C86.8196 92.4556 76.1826 20.5326 76.1136 20.0386C76.0466 19.6076 75.6926 19.2786 75.2566 19.2426Z' fill='#5F8E3E'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M46.2185 35.9847L42.4805 47.1007C40.2095 45.9897 37.7225 45.3937 35.1955 45.3537C29.3175 45.3537 29.0135 49.0457 29.0135 49.9767C29.0135 55.0527 42.2505 57.0037 42.2505 68.8857C42.2505 78.2377 36.3165 84.2647 28.3135 84.2647C22.8485 84.4077 17.5765 82.2377 13.7965 78.2897L16.3695 69.7967C16.3695 69.7967 21.4175 74.1277 25.6775 74.1277C27.7505 74.2157 29.5025 72.6067 29.5905 70.5337C29.5935 70.4687 29.5945 70.4027 29.5935 70.3377C29.5935 63.7157 18.7295 63.4187 18.7295 52.5397C18.7295 43.3827 25.3035 34.5257 38.5765 34.5257C41.2045 34.3897 43.8255 34.8897 46.2185 35.9847Z' fill='white'></path></symbol></defs></svg></div></div><button type='button' class='shopify-payment-button__button shopify-payment-button__button--unbranded _2ogcW-Q9I-rgsSkNbRiJzA _2EiMjnumZ6FVtlC7RViKtj _2-dUletcCZ2ZL1aaH0GXxT' data-testid='Checkout-button'>Buy it now</button><button aria-disabled='true' aria-hidden='true' class='shopify-payment-button__more-options _2ogcW-Q9I-rgsSkNbRiJzA shopify-payment-button__button--hidden' type='button' data-testid='sheet-open-button'>More payment options</button></div></div></div></div>

                    </div>
                  </form>";

                $shopifyService->updateCartTemplate($html, 'x.com');
        */
        /*
         * <div data-shopify="payment-button" class="shopify-payment-button"><div><div><div><div class="shopify-cleanslate"><div id="shopify-svg-symbols" class="VoW3UuJKYxZJHMpUkDNUv" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" focusable="false"><defs><symbol id="shopify-svg__warning" viewBox="0 0 16 14"><path d="M5.925 2.344c1.146-1.889 3.002-1.893 4.149 0l4.994 8.235c1.146 1.889.288 3.421-1.916 3.421h-10.305c-2.204 0-3.063-1.529-1.916-3.421l4.994-8.235zm1.075 1.656v5h2v-5h-2zm0 6v2h2v-2h-2z"></path></symbol><symbol id="shopify-svg__loading" viewBox="0 0 32 32"><path d="M32 16c0 8.837-7.163 16-16 16S0 24.837 0 16 7.163 0 16 0v2C8.268 2 2 8.268 2 16s6.268 14 14 14 14-6.268 14-14h2z"></path></symbol><symbol id="shopify-svg__error" viewBox="0 0 18 18"><path fill="#FF3E3E" d="M9 18c5 0 9-4 9-9s-4-9-9-9-9 4-9 9 4 9 9 9z"></path><path fill="#FFFFFF" d="M8 4h2v6H8z"></path><rect fill="#FFFFFF" x="7.8" y="12" width="2.5" height="2.5" rx="1.3"></rect></symbol><symbol id="shopify-svg__close-circle" viewBox="0 0 16 16"><circle cx="8" cy="8" r="8"></circle><path d="M10.5 5.5l-5 5M5.5 5.5l5 5" stroke="#FFF" stroke-width="1.5" stroke-linecap="square"></path></symbol><symbol id="shopify-svg__close" viewBox="0 0 20 20"><path d="M17.1 4.3l-1.4-1.4-5.7 5.7-5.7-5.7-1.4 1.4 5.7 5.7-5.7 5.7 1.4 1.4 5.7-5.7 5.7 5.7 1.4-1.4-5.7-5.7z"></path></symbol><symbol id="shopify-svg__arrow-right" viewBox="0 0 16 16"><path d="M16 8.1l-8.1 8.1-1.1-1.1L13 8.9H0V7.3h13L6.8 1.1 7.9 0 16 8.1z"></path></symbol><symbol id="shopify-svg__payments-google-pay-light" viewBox="0 0 41 17"><path fill="rgba(255, 255, 255, 1)" d="M19.526 2.635v4.083h2.518c.6 0 1.096-.202 1.488-.605.403-.402.605-.882.605-1.437 0-.544-.202-1.018-.605-1.422-.392-.413-.888-.62-1.488-.62h-2.518zm0 5.52v4.736h-1.504V1.198h3.99c1.013 0 1.873.337 2.582 1.012.72.675 1.08 1.497 1.08 2.466 0 .991-.36 1.819-1.08 2.482-.697.665-1.559.996-2.583.996h-2.485v.001zM27.194 10.442c0 .392.166.718.499.98.332.26.722.391 1.168.391.633 0 1.196-.234 1.692-.701.497-.469.744-1.019.744-1.65-.469-.37-1.123-.555-1.962-.555-.61 0-1.12.148-1.528.442-.409.294-.613.657-.613 1.093m1.946-5.815c1.112 0 1.989.297 2.633.89.642.594.964 1.408.964 2.442v4.932h-1.439v-1.11h-.065c-.622.914-1.45 1.372-2.486 1.372-.882 0-1.621-.262-2.215-.784-.594-.523-.891-1.176-.891-1.96 0-.828.313-1.486.94-1.976s1.463-.735 2.51-.735c.892 0 1.629.163 2.206.49v-.344c0-.522-.207-.966-.621-1.33a2.132 2.132 0 0 0-1.455-.547c-.84 0-1.504.353-1.995 1.062l-1.324-.834c.73-1.045 1.81-1.568 3.238-1.568M40.993 4.889l-5.02 11.53H34.42l1.864-4.034-3.302-7.496h1.635l2.387 5.749h.032l2.322-5.75z"></path><path fill="#4285F4" d="M13.448 7.134c0-.473-.04-.93-.116-1.366H6.988v2.588h3.634a3.11 3.11 0 0 1-1.344 2.042v1.68h2.169c1.27-1.17 2.001-2.9 2.001-4.944"></path><path fill="#34A853" d="M6.988 13.7c1.816 0 3.344-.595 4.459-1.621l-2.169-1.681c-.603.406-1.38.643-2.29.643-1.754 0-3.244-1.182-3.776-2.774H.978v1.731a6.728 6.728 0 0 0 6.01 3.703"></path><path fill="#FBBC05" d="M3.212 8.267a4.034 4.034 0 0 1 0-2.572V3.964H.978A6.678 6.678 0 0 0 .261 6.98c0 1.085.26 2.11.717 3.017l2.234-1.731z"></path><path fill="#EA4335" d="M6.988 2.921c.992 0 1.88.34 2.58 1.008v.001l1.92-1.918C10.324.928 8.804.262 6.989.262a6.728 6.728 0 0 0-6.01 3.702l2.234 1.731c.532-1.592 2.022-2.774 3.776-2.774"></path></symbol><symbol id="shopify-svg__payments-google-pay-dark" viewBox="0 0 41 17"><path fill="rgba(0, 0, 0, .55)" d="M19.526 2.635v4.083h2.518c.6 0 1.096-.202 1.488-.605.403-.402.605-.882.605-1.437 0-.544-.202-1.018-.605-1.422-.392-.413-.888-.62-1.488-.62h-2.518zm0 5.52v4.736h-1.504V1.198h3.99c1.013 0 1.873.337 2.582 1.012.72.675 1.08 1.497 1.08 2.466 0 .991-.36 1.819-1.08 2.482-.697.665-1.559.996-2.583.996h-2.485v.001zM27.194 10.442c0 .392.166.718.499.98.332.26.722.391 1.168.391.633 0 1.196-.234 1.692-.701.497-.469.744-1.019.744-1.65-.469-.37-1.123-.555-1.962-.555-.61 0-1.12.148-1.528.442-.409.294-.613.657-.613 1.093m1.946-5.815c1.112 0 1.989.297 2.633.89.642.594.964 1.408.964 2.442v4.932h-1.439v-1.11h-.065c-.622.914-1.45 1.372-2.486 1.372-.882 0-1.621-.262-2.215-.784-.594-.523-.891-1.176-.891-1.96 0-.828.313-1.486.94-1.976s1.463-.735 2.51-.735c.892 0 1.629.163 2.206.49v-.344c0-.522-.207-.966-.621-1.33a2.132 2.132 0 0 0-1.455-.547c-.84 0-1.504.353-1.995 1.062l-1.324-.834c.73-1.045 1.81-1.568 3.238-1.568M40.993 4.889l-5.02 11.53H34.42l1.864-4.034-3.302-7.496h1.635l2.387 5.749h.032l2.322-5.75z"></path><path fill="#4285F4" d="M13.448 7.134c0-.473-.04-.93-.116-1.366H6.988v2.588h3.634a3.11 3.11 0 0 1-1.344 2.042v1.68h2.169c1.27-1.17 2.001-2.9 2.001-4.944"></path><path fill="#34A853" d="M6.988 13.7c1.816 0 3.344-.595 4.459-1.621l-2.169-1.681c-.603.406-1.38.643-2.29.643-1.754 0-3.244-1.182-3.776-2.774H.978v1.731a6.728 6.728 0 0 0 6.01 3.703"></path><path fill="#FBBC05" d="M3.212 8.267a4.034 4.034 0 0 1 0-2.572V3.964H.978A6.678 6.678 0 0 0 .261 6.98c0 1.085.26 2.11.717 3.017l2.234-1.731z"></path><path fill="#EA4335" d="M6.988 2.921c.992 0 1.88.34 2.58 1.008v.001l1.92-1.918C10.324.928 8.804.262 6.989.262a6.728 6.728 0 0 0-6.01 3.702l2.234 1.731c.532-1.592 2.022-2.774 3.776-2.774"></path></symbol><symbol id="shopify-svg__payments-amazon-pay" viewBox="0 0 102 20"><path fill="#333e48" d="M75.19 1.786c-.994 0-1.933.326-2.815.98v5.94c.896.683 1.82 1.023 2.774 1.023 1.932 0 2.899-1.32 2.899-3.96 0-2.655-.953-3.983-2.858-3.983zm-2.962-.277A5.885 5.885 0 0 1 73.93.444a4.926 4.926 0 0 1 1.85-.362c.672 0 1.282.127 1.827.383a3.763 3.763 0 0 1 1.387 1.108c.378.482.669 1.068.872 1.757.203.689.305 1.466.305 2.332 0 .88-.109 1.675-.326 2.385-.217.71-.522 1.314-.914 1.81a4.137 4.137 0 0 1-1.429 1.16 4.165 4.165 0 0 1-1.87.416c-1.26 0-2.346-.419-3.256-1.256v4.983c0 .284-.14.426-.42.426h-1.24c-.28 0-.42-.142-.42-.426V.827c0-.284.14-.426.42-.426h.925c.28 0 .441.142.483.426l.105.682zm13.194 8.37a4.21 4.21 0 0 0 1.45-.277 5.463 5.463 0 0 0 1.45-.81V6.62c-.35-.085-.719-.152-1.104-.202a8.8 8.8 0 0 0-1.124-.075c-1.583 0-2.374.617-2.374 1.853 0 .54.147.955.441 1.246.294.29.715.437 1.261.437zm-2.458-7.625l-.158.053a.561.561 0 0 1-.179.033c-.182 0-.273-.128-.273-.384V1.38c0-.199.028-.337.084-.415.056-.078.169-.153.337-.224.448-.199 1-.359 1.66-.48.657-.12 1.316-.18 1.974-.18 1.33 0 2.311.277 2.942.83.63.554.945 1.413.945 2.577v7.284c0 .284-.14.426-.42.426h-.903c-.267 0-.42-.135-.463-.405l-.105-.702a5.74 5.74 0 0 1-1.67 1.022 4.908 4.908 0 0 1-1.817.362c-1.009 0-1.807-.288-2.395-.863-.589-.575-.883-1.345-.883-2.31 0-1.037.364-1.864 1.092-2.481.73-.618 1.71-.927 2.942-.927.784 0 1.667.12 2.647.362V3.852c0-.767-.168-1.307-.504-1.619-.336-.313-.925-.469-1.764-.469-.982 0-2.01.163-3.09.49zm14.16 10.84c-.379.98-.816 1.683-1.314 2.109-.496.426-1.144.639-1.943.639-.448 0-.847-.05-1.197-.15a.606.606 0 0 1-.336-.202c-.07-.093-.105-.237-.105-.437V14.5c0-.27.105-.405.315-.405.07 0 .175.014.315.043.14.028.33.043.567.043.532 0 .946-.128 1.24-.384.294-.255.56-.724.798-1.406l.4-1.086-4.056-10.137c-.098-.241-.146-.411-.146-.511 0-.17.097-.256.294-.256h1.26c.224 0 .378.036.463.106.083.072.167.228.251.47l2.942 8.263L99.708.976c.084-.24.168-.397.252-.469.084-.07.238-.106.462-.106h1.177c.196 0 .294.086.294.256 0 .1-.05.27-.147.51l-4.622 11.927M40.15 15.47c-3.761 2.814-9.216 4.31-13.912 4.31-6.583 0-12.51-2.466-16.996-6.572-.352-.322-.038-.763.385-.513 4.84 2.855 10.825 4.574 17.006 4.574 4.17 0 8.753-.877 12.971-2.691.636-.273 1.17.425.547.891"></path><path fill="#333e48" d="M41.717 13.657c-.482-.624-3.181-.296-4.394-.148-.368.044-.425-.281-.093-.517 2.153-1.533 5.682-1.09 6.092-.577.413.518-.108 4.104-2.127 5.816-.31.263-.605.122-.468-.225.455-1.15 1.471-3.724.99-4.349M37.429 2.06V.57A.365.365 0 0 1 37.8.193l6.59-.001c.21 0 .38.155.38.376v1.278c-.003.214-.18.494-.496.938L40.86 7.722c1.267-.03 2.607.163 3.757.818.26.148.33.367.35.582v1.59c0 .218-.237.472-.485.34-2.028-1.077-4.718-1.194-6.96.013-.23.124-.47-.126-.47-.345V9.209c0-.242.005-.656.246-1.024l3.953-5.75H37.81a.369.369 0 0 1-.38-.375M13.4 11.365h-2.005a.38.38 0 0 1-.358-.343L11.038.595a.38.38 0 0 1 .387-.375h1.866a.38.38 0 0 1 .365.35v1.36h.037C14.18.615 15.096 0 16.331 0c1.253 0 2.039.614 2.6 1.93C19.418.615 20.521 0 21.7 0c.842 0 1.758.351 2.32 1.141.635.878.505 2.15.505 3.27l-.002 6.58a.38.38 0 0 1-.387.374h-2.001a.378.378 0 0 1-.36-.374V5.463c0-.438.037-1.535-.056-1.952-.15-.703-.6-.9-1.179-.9-.486 0-.991.33-1.197.855-.206.527-.188 1.405-.188 1.997v5.527a.38.38 0 0 1-.386.375h-2.002a.379.379 0 0 1-.36-.374l-.001-5.528c0-1.163.186-2.874-1.235-2.874-1.44 0-1.384 1.668-1.384 2.874l-.001 5.527a.38.38 0 0 1-.387.375m37.059-9.236c-1.478 0-1.571 2.04-1.571 3.312 0 1.273-.02 3.993 1.552 3.993 1.554 0 1.628-2.194 1.628-3.532 0-.877-.038-1.93-.3-2.764-.224-.724-.673-1.01-1.31-1.01zM50.439 0c2.975 0 4.584 2.59 4.584 5.88 0 3.181-1.777 5.705-4.584 5.705-2.918 0-4.508-2.59-4.508-5.814C45.93 2.523 47.539 0 50.439 0zm8.441 11.365h-1.997a.379.379 0 0 1-.36-.374L56.52.561a.381.381 0 0 1 .386-.34L58.764.22c.175.009.32.13.356.291v1.595h.038C59.72.68 60.505 0 61.89 0c.898 0 1.778.329 2.339 1.229.524.834.524 2.237.524 3.247v6.561a.382.382 0 0 1-.385.328H62.36a.38.38 0 0 1-.357-.328V5.376c0-1.141.13-2.809-1.253-2.809-.487 0-.936.33-1.16.834-.281.636-.319 1.272-.319 1.975v5.614a.386.386 0 0 1-.39.375m-24.684.075a.41.41 0 0 1-.473.047c-.665-.56-.785-.82-1.149-1.354-1.1 1.136-1.879 1.477-3.304 1.477-1.687 0-3-1.055-3-3.166 0-1.65.882-2.77 2.138-3.32 1.087-.484 2.606-.572 3.769-.704v-.264c0-.484.037-1.055-.245-1.473-.243-.374-.712-.528-1.124-.528-.765 0-1.444.397-1.611 1.22-.035.183-.167.364-.348.374l-1.943-.214c-.164-.037-.346-.17-.299-.425C27.055.721 29.183 0 31.09 0c.975 0 2.25.263 3.018 1.011.975.924.881 2.155.881 3.497v3.165c0 .952.39 1.37.757 1.882.128.185.156.405-.007.54-.409.348-1.136.988-1.537 1.35l-.005-.005zm-2.02-4.953v-.44c-1.45 0-2.98.314-2.98 2.045 0 .88.45 1.473 1.218 1.473.562 0 1.069-.352 1.387-.923.394-.704.376-1.363.376-2.155zM7.926 11.44a.41.41 0 0 1-.473.047c-.667-.56-.786-.82-1.15-1.354C5.204 11.27 4.425 11.61 3 11.61c-1.688 0-3-1.055-3-3.166 0-1.65.88-2.77 2.137-3.32 1.087-.484 2.606-.572 3.768-.704v-.264c0-.484.038-1.055-.243-1.473-.244-.374-.713-.528-1.125-.528-.764 0-1.444.397-1.61 1.22-.036.183-.168.364-.35.374l-1.94-.214c-.165-.037-.347-.17-.3-.425C.783.721 2.911 0 4.818 0c.975 0 2.25.263 3.018 1.011.975.924.882 2.155.882 3.497v3.165c0 .952.39 1.37.756 1.882.128.185.157.405-.006.54a78.47 78.47 0 0 0-1.537 1.35l-.005-.005zm-2.02-4.953v-.44c-1.45 0-2.982.314-2.982 2.045 0 .88.45 1.473 1.219 1.473.562 0 1.069-.352 1.387-.923.394-.704.375-1.363.375-2.155z"></path></symbol><symbol id="shopify-svg__payments-apple-pay-dark" viewBox="0 0 43 19"><path fill="#000000" d="M6.948 1.409C7.934.147 9.305.147 9.305.147s.193 1.18-.771 2.316c-1.05 1.2-2.228.993-2.228.993s-.236-.93.642-2.047zM3.82 3.663c-1.735 0-3.6 1.51-3.6 4.363 0 2.916 2.186 6.555 3.943 6.555.6 0 1.543-.6 2.485-.6.922 0 1.607.559 2.464.559 1.907 0 3.322-3.826 3.322-3.826s-2.015-.744-2.015-2.936c0-1.944 1.629-2.73 1.629-2.73s-.836-1.447-2.936-1.447c-1.22 0-2.164.661-2.656.661-.622.021-1.5-.6-2.636-.6zM19.64 1.426c2.453 0 4.188 1.788 4.188 4.396 0 2.608-1.755 4.417-4.248 4.417h-2.932v4.564h-1.974V1.426h4.966zm-2.992 7.067h2.473c1.695 0 2.693-.967 2.693-2.65 0-1.683-.978-2.671-2.693-2.671h-2.473v5.321zm7.559 3.429c0-1.767 1.296-2.777 3.65-2.945l2.572-.147v-.78c0-1.156-.738-1.787-1.994-1.787-1.037 0-1.795.568-1.955 1.43h-1.775c.06-1.788 1.656-3.092 3.79-3.092 2.333 0 3.829 1.304 3.829 3.281v6.9h-1.815v-1.684h-.04c-.519 1.094-1.715 1.788-3.012 1.788-1.934.021-3.25-1.178-3.25-2.965zm6.222-.905v-.778l-2.313.168c-1.297.084-1.975.59-1.975 1.494 0 .862.718 1.409 1.815 1.409 1.396-.021 2.473-.968 2.473-2.293zm3.969 7.383v-1.64c.14.041.438.041.598.041.897 0 1.416-.4 1.735-1.472l.14-.526L33.4 4.707h2.054l2.453 8.224h.04L40.4 4.707h1.994l-3.57 10.538c-.818 2.419-1.715 3.197-3.67 3.197-.14.02-.598-.021-.757-.042z"></path></symbol><symbol id="shopify-svg__payments-apple-pay-light" viewBox="0 0 43 19"><path fill="#FFFFFF" d="M6.948 1.409C7.934.147 9.305.147 9.305.147s.193 1.18-.771 2.316c-1.05 1.2-2.228.993-2.228.993s-.236-.93.642-2.047zM3.82 3.663c-1.735 0-3.6 1.51-3.6 4.363 0 2.916 2.186 6.555 3.943 6.555.6 0 1.543-.6 2.485-.6.922 0 1.607.559 2.464.559 1.907 0 3.322-3.826 3.322-3.826s-2.015-.744-2.015-2.936c0-1.944 1.629-2.73 1.629-2.73s-.836-1.447-2.936-1.447c-1.22 0-2.164.661-2.656.661-.622.021-1.5-.6-2.636-.6zM19.64 1.426c2.453 0 4.188 1.788 4.188 4.396 0 2.608-1.755 4.417-4.248 4.417h-2.932v4.564h-1.974V1.426h4.966zm-2.992 7.067h2.473c1.695 0 2.693-.967 2.693-2.65 0-1.683-.978-2.671-2.693-2.671h-2.473v5.321zm7.559 3.429c0-1.767 1.296-2.777 3.65-2.945l2.572-.147v-.78c0-1.156-.738-1.787-1.994-1.787-1.037 0-1.795.568-1.955 1.43h-1.775c.06-1.788 1.656-3.092 3.79-3.092 2.333 0 3.829 1.304 3.829 3.281v6.9h-1.815v-1.684h-.04c-.519 1.094-1.715 1.788-3.012 1.788-1.934.021-3.25-1.178-3.25-2.965zm6.222-.905v-.778l-2.313.168c-1.297.084-1.975.59-1.975 1.494 0 .862.718 1.409 1.815 1.409 1.396-.021 2.473-.968 2.473-2.293zm3.969 7.383v-1.64c.14.041.438.041.598.041.897 0 1.416-.4 1.735-1.472l.14-.526L33.4 4.707h2.054l2.453 8.224h.04L40.4 4.707h1.994l-3.57 10.538c-.818 2.419-1.715 3.197-3.67 3.197-.14.02-.598-.021-.757-.042z"></path></symbol><symbol id="shopify-svg__payments-paypal" viewBox="0 0 67 19"><path fill="#253b80" d="M8.44.57H3.29a.718.718 0 0 0-.707.61L.502 14.517c-.041.263.16.5.425.5h2.458a.718.718 0 0 0 .707-.61l.561-3.597a.717.717 0 0 1 .706-.611h1.63c3.391 0 5.349-1.658 5.86-4.944.23-1.437.01-2.566-.657-3.357C11.461 1.029 10.162.57 8.44.57zm.594 4.87C8.752 7.308 7.34 7.308 5.976 7.308h-.777l.545-3.485a.43.43 0 0 1 .424-.366h.356c.93 0 1.807 0 2.26.535.27.32.353.794.25 1.45zm14.796-.06h-2.466a.43.43 0 0 0-.424.367l-.109.696-.172-.252c-.534-.783-1.724-1.044-2.912-1.044-2.725 0-5.052 2.084-5.505 5.008-.235 1.46.1 2.854.919 3.827.75.894 1.826 1.267 3.105 1.267 2.195 0 3.412-1.426 3.412-1.426l-.11.692a.432.432 0 0 0 .424.502h2.22a.718.718 0 0 0 .707-.61l1.333-8.526a.43.43 0 0 0-.423-.5zm-3.437 4.849c-.238 1.422-1.356 2.378-2.782 2.378-.716 0-1.288-.232-1.655-.672-.365-.436-.503-1.058-.387-1.75.222-1.41 1.359-2.397 2.763-2.397.7 0 1.269.235 1.644.678.375.448.524 1.073.417 1.763zM36.96 5.38h-2.478a.716.716 0 0 0-.592.318l-3.417 5.085-1.448-4.887a.719.719 0 0 0-.687-.515h-2.435a.433.433 0 0 0-.407.573l2.73 8.09-2.567 3.66a.434.434 0 0 0 .35.684h2.475a.712.712 0 0 0 .588-.31l8.24-12.016a.434.434 0 0 0-.352-.681z"></path><path fill="#179bd7" d="M45.163.57h-5.15a.717.717 0 0 0-.706.61l-2.082 13.337a.43.43 0 0 0 .423.5h2.642a.502.502 0 0 0 .494-.427l.591-3.78a.717.717 0 0 1 .706-.611h1.63c3.392 0 5.348-1.658 5.86-4.944.231-1.437.009-2.566-.657-3.357C48.183 1.029 46.886.57 45.163.57zm.593 4.87c-.28 1.867-1.692 1.867-3.057 1.867h-.777l.546-3.485a.429.429 0 0 1 .423-.366h.356c.93 0 1.807 0 2.26.535.27.32.353.794.25 1.45zm14.795-.06h-2.464a.428.428 0 0 0-.423.367l-.109.696-.173-.252c-.534-.783-1.723-1.044-2.911-1.044-2.724 0-5.05 2.084-5.504 5.008-.235 1.46.099 2.854.918 3.827.753.894 1.826 1.267 3.105 1.267 2.195 0 3.413-1.426 3.413-1.426l-.11.692a.432.432 0 0 0 .424.502h2.22a.717.717 0 0 0 .707-.61l1.333-8.526a.433.433 0 0 0-.426-.5zm-3.436 4.849c-.237 1.422-1.356 2.378-2.782 2.378-.714 0-1.288-.232-1.655-.672-.365-.436-.502-1.058-.387-1.75.223-1.41 1.359-2.397 2.763-2.397.7 0 1.269.235 1.644.678.377.448.526 1.073.417 1.763zM63.458.935l-2.113 13.582a.43.43 0 0 0 .423.5h2.124a.716.716 0 0 0 .707-.61L66.683 1.07a.432.432 0 0 0-.423-.5h-2.379c-.21 0-.39.156-.423.366z"></path></symbol><symbol id="shopify-svg__payments-shopify-pay-dark" viewBox="0 0 264 115"><path fill-rule="evenodd" clip-rule="evenodd" d="M129.553 53.8275C130.909 54.0355 132.266 54.2445 134.354 54.2445C143.538 54.2445 148.548 47.3565 148.548 40.5715C148.548 34.4135 144.478 31.9095 138.842 31.9095C136.546 31.9095 134.772 32.0135 133.623 32.3265L129.553 53.8275ZM121.516 21.9935C125.9 20.9495 132.476 20.3245 138.529 20.3245C144.374 20.3245 151.575 21.3675 156.481 25.2295C160.656 28.4645 163.056 33.4745 163.056 39.4245C163.056 48.5045 158.777 55.3925 153.662 59.5675C148.236 63.9515 140.407 65.9345 132.58 65.9345C130.597 65.9345 128.718 65.7255 127.362 65.6215L122.456 91.0885H108.366L121.516 21.9935Z" fill="black"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M194.628 50.2782C193.48 49.9662 192.228 49.8612 190.976 49.8612C181.582 49.8612 175.215 63.3252 175.215 72.1972C175.215 77.1022 176.78 80.4422 180.643 80.4422C184.922 80.4422 189.827 75.1192 192.02 63.5342L194.628 50.2782ZM190.14 91.0882C190.244 88.1662 190.558 84.9302 190.767 81.4862H190.453C185.966 89.4182 179.912 92.1322 174.589 92.1322C165.926 92.1322 160.395 85.2432 160.395 75.1192C160.395 58.4202 170.727 38.9022 194.941 38.9022C200.682 38.9022 206.735 40.0502 210.701 41.3022L205.274 68.5442C204.022 75.0142 203.082 84.9302 203.187 91.0882H190.14Z" fill="black"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M231.419 41.3724L233.715 63.0124C234.342 68.1264 234.759 71.7794 234.968 75.1194H235.177C236.325 71.6754 237.369 68.4394 239.456 62.9074L248.223 41.3724H263.253L245.614 77.8324C239.352 90.7754 233.299 100.272 226.723 106.431C221.608 111.232 215.555 113.632 212.633 114.259L208.667 102.047C211.066 101.212 214.094 99.9594 216.808 97.9764C220.148 95.6804 222.966 92.5494 224.635 89.3134C225.053 88.5834 225.157 87.9564 224.948 86.9134L216.963 41.3724H231.419Z" fill="black"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M40.3083 4.3368C40.8933 4.3498 41.4583 4.5408 41.9323 4.8838C37.8763 6.7928 33.5313 11.5948 31.6953 21.1858L23.9653 23.5788C26.1183 16.2638 31.2223 4.3368 40.3083 4.3368ZM44.0703 7.8938C45.0193 10.6298 45.4523 13.5188 45.3473 16.4128V16.9628L35.5713 19.9818C37.4543 12.7258 40.9833 9.2168 44.0683 7.8938H44.0703ZM53.4023 14.4708L48.6703 15.9358V14.9118C48.7243 12.3178 48.3403 9.7358 47.5403 7.2718C50.3373 7.6238 52.2073 10.8058 53.4003 14.4688L53.4023 14.4708ZM76.1213 20.0368C76.0533 19.6068 75.6993 19.2778 75.2643 19.2428C74.9093 19.2128 67.3683 18.6548 67.3683 18.6548C67.3683 18.6548 62.1283 13.4578 61.5553 12.8838C60.9283 12.4588 60.1373 12.3578 59.4233 12.6098L56.4933 13.5148C54.7443 8.4838 51.6573 3.8598 46.2263 3.8598C46.0763 3.8598 45.9213 3.8668 45.7673 3.8748C44.6183 2.1488 42.7243 1.0648 40.6553 0.945801C28.0003 0.945801 21.9593 16.7558 20.0643 24.7898C15.1493 26.3118 11.6593 27.3928 11.2103 27.5338C8.46731 28.3968 8.38131 28.4808 8.01631 31.0648C7.74731 33.0248 0.569305 88.5248 0.569305 88.5248L56.5163 99.0038L86.8313 92.4458C86.8313 92.4458 76.1883 20.5328 76.1213 20.0368Z" fill="#95BF46"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M75.2644 19.2426C74.9094 19.2126 67.3684 18.6556 67.3684 18.6556C67.3684 18.6556 62.1284 13.4596 61.5554 12.8836C61.3324 12.6776 61.0494 12.5476 60.7484 12.5126L56.5184 99.0106L86.8274 92.4556C86.8274 92.4556 76.1894 20.5326 76.1214 20.0386C76.0534 19.6076 75.7004 19.2786 75.2644 19.2426Z" fill="#5F8E3E"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M46.2263 35.9847L42.4883 47.1007C40.2173 45.9897 37.7303 45.3937 35.2033 45.3537C29.3243 45.3537 29.0213 49.0457 29.0213 49.9767C29.0213 55.0527 42.2583 57.0037 42.2583 68.8857C42.2583 78.2377 36.3243 84.2647 28.3203 84.2647C22.8563 84.4077 17.5843 82.2377 13.8043 78.2897L16.3763 69.7967C16.3763 69.7967 21.4253 74.1277 25.6853 74.1277C27.7573 74.2157 29.5093 72.6067 29.5983 70.5337C29.6013 70.4687 29.6023 70.4027 29.6013 70.3377C29.6013 63.7157 18.7373 63.4187 18.7373 52.5397C18.7373 43.3827 25.3113 34.5257 38.5843 34.5257C41.2123 34.3897 43.8333 34.8897 46.2263 35.9847Z" fill="white"></path></symbol><symbol id="shopify-svg__payments-shopify-pay-light" viewBox="0 0 264 115"><path fill-rule="evenodd" clip-rule="evenodd" d="M129.545 53.8275C130.902 54.0355 132.259 54.2445 134.346 54.2445C143.53 54.2445 148.54 47.3565 148.54 40.5715C148.54 34.4135 144.47 31.9095 138.834 31.9095C136.538 31.9095 134.764 32.0135 133.616 32.3265L129.545 53.8275ZM121.508 21.9935C125.892 20.9495 132.468 20.3245 138.521 20.3245C144.366 20.3245 151.567 21.3675 156.473 25.2295C160.648 28.4645 163.048 33.4745 163.048 39.4245C163.048 48.5045 158.769 55.3925 153.654 59.5675C148.228 63.9515 140.399 65.9345 132.572 65.9345C130.589 65.9345 128.71 65.7255 127.354 65.6215L122.448 91.0885H108.358L121.508 21.9935Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M194.62 50.2782C193.472 49.9662 192.22 49.8612 190.968 49.8612C181.574 49.8612 175.207 63.3252 175.207 72.1972C175.207 77.1022 176.772 80.4422 180.635 80.4422C184.914 80.4422 189.819 75.1192 192.012 63.5342L194.62 50.2782ZM190.133 91.0882C190.237 88.1662 190.55 84.9302 190.759 81.4862H190.445C185.958 89.4182 179.904 92.1322 174.581 92.1322C165.918 92.1322 160.387 85.2432 160.387 75.1192C160.387 58.4202 170.72 38.9022 194.933 38.9022C200.674 38.9022 206.727 40.0502 210.693 41.3022L205.266 68.5442C204.014 75.0142 203.075 84.9302 203.18 91.0882H190.133Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M231.412 41.3724L233.708 63.0124C234.335 68.1264 234.751 71.7794 234.96 75.1194H235.169C236.317 71.6754 237.361 68.4394 239.449 62.9074L248.216 41.3724H263.245L245.606 77.8324C239.345 90.7754 233.291 100.272 226.715 106.431C221.6 111.232 215.547 113.632 212.625 114.259L208.659 102.047C211.058 101.212 214.086 99.9594 216.8 97.9764C220.14 95.6804 222.958 92.5494 224.627 89.3134C225.046 88.5834 225.15 87.9564 224.941 86.9134L216.955 41.3724H231.412Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M40.3005 4.3368C40.8855 4.3498 41.4505 4.5408 41.9245 4.8838C37.8685 6.7928 33.5235 11.5948 31.6885 21.1858L23.9575 23.5788C26.1115 16.2638 31.2145 4.3368 40.3005 4.3368ZM44.0625 7.8938C45.0115 10.6298 45.4445 13.5188 45.3395 16.4128V16.9628L35.5635 19.9818C37.4465 12.7258 40.9755 9.2168 44.0605 7.8938H44.0625ZM53.3945 14.4708L48.6625 15.9358V14.9118C48.7165 12.3178 48.3325 9.7358 47.5325 7.2718C50.3295 7.6238 52.1995 10.8058 53.3925 14.4688L53.3945 14.4708ZM76.1135 20.0368C76.0455 19.6068 75.6915 19.2778 75.2565 19.2428C74.9015 19.2128 67.3605 18.6548 67.3605 18.6548C67.3605 18.6548 62.1205 13.4578 61.5485 12.8838C60.9205 12.4588 60.1295 12.3578 59.4155 12.6098L56.4855 13.5148C54.7365 8.4838 51.6495 3.8598 46.2185 3.8598C46.0685 3.8598 45.9135 3.8668 45.7605 3.8748C44.6105 2.1488 42.7165 1.0648 40.6475 0.945801C27.9935 0.945801 21.9515 16.7558 20.0565 24.7898C15.1415 26.3118 11.6515 27.3928 11.2035 27.5338C8.45952 28.3968 8.37352 28.4808 8.00852 31.0648C7.73952 33.0248 0.561523 88.5248 0.561523 88.5248L56.5085 99.0038L86.8235 92.4458C86.8235 92.4458 76.1805 20.5328 76.1135 20.0368Z" fill="#95BF46"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M75.2566 19.2426C74.9016 19.2126 67.3606 18.6556 67.3606 18.6556C67.3606 18.6556 62.1206 13.4596 61.5486 12.8836C61.3246 12.6776 61.0426 12.5476 60.7406 12.5126L56.5106 99.0106L86.8196 92.4556C86.8196 92.4556 76.1826 20.5326 76.1136 20.0386C76.0466 19.6076 75.6926 19.2786 75.2566 19.2426Z" fill="#5F8E3E"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M46.2185 35.9847L42.4805 47.1007C40.2095 45.9897 37.7225 45.3937 35.1955 45.3537C29.3175 45.3537 29.0135 49.0457 29.0135 49.9767C29.0135 55.0527 42.2505 57.0037 42.2505 68.8857C42.2505 78.2377 36.3165 84.2647 28.3135 84.2647C22.8485 84.4077 17.5765 82.2377 13.7965 78.2897L16.3695 69.7967C16.3695 69.7967 21.4175 74.1277 25.6775 74.1277C27.7505 74.2157 29.5025 72.6067 29.5905 70.5337C29.5935 70.4687 29.5945 70.4027 29.5935 70.3377C29.5935 63.7157 18.7295 63.4187 18.7295 52.5397C18.7295 43.3827 25.3035 34.5257 38.5765 34.5257C41.2045 34.3897 43.8255 34.8897 46.2185 35.9847Z" fill="white"></path></symbol></defs></svg></div></div><button type="button" class="shopify-payment-button__button shopify-payment-button__button--unbranded _2ogcW-Q9I-rgsSkNbRiJzA _2EiMjnumZ6FVtlC7RViKtj _2-dUletcCZ2ZL1aaH0GXxT" data-testid="Checkout-button">Buy it now</button><button aria-disabled="true" aria-hidden="true" class="shopify-payment-button__more-options _2ogcW-Q9I-rgsSkNbRiJzA shopify-payment-button__button--hidden" type="button" data-testid="sheet-open-button">More payment options</button></div></div></div></div>
         */

        //        $shopifyIntegrationModel = new ShopifyIntegration();
        //
        //        $integrations = $shopifyIntegrationModel->has('project')->get();

        //dd($integrations);

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
        //$shopifyService->importShopifyStore(154, auth()->user()->account_owner_id);
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
                                                                                    ->getTrackingStatusEnum('posted'),
                                     'tracking_code'        => $sale->delivery->tracking_code,
                                 ]);
            }
        }

        return 'Pronto!';
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
                    'debit_card_release_money_days'      => "1",
                    'boleto_release_money_days'           => "1",
                    'boleto_tax'                          => "1",
                    'credit_card_tax'                     => "1",
                    'debit_card_tax'                     => "1",
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
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function documentStatus()
    {
        $userDocumentModel = new UserDocument();
        $userModel         = new User();
        $companyDocument   = new CompanyDocument();
        $companyModel      = new Company();

        //Verifica os documentos aprovados do usuario
        $userAddressDocuments  = $userDocumentModel->where('status', 3)
                                                   ->where('document_type_enum', $userModel->present()
                                                                                           ->getDocumentType('address_document'))
                                                   ->with('user')
                                                   ->whereHas('user', function($query) {
                                                       $query->where('address_document_status', '!=', 3);
                                                   })->get();
        $userPersonalDocuments = $userDocumentModel->where('status', 3)
                                                   ->where('document_type_enum', $userModel->present()
                                                                                           ->getDocumentType('personal_document'))
                                                   ->with('user')
                                                   ->whereHas('user', function($query) {
                                                       $query->where('personal_document_status', '!=', 3);
                                                   })->get();

        foreach ($userAddressDocuments as $document) {
            $document->user->update(['address_document_status' => $document->status]);
        }
        foreach ($userPersonalDocuments as $document) {
            $document->user->update(['personal_document_status' => $document->status]);
        }

        //Verifica os documentos aprovados da empresa
        $companyBankDocuments    = $companyDocument->where('status', 3)->with('company')
                                                   ->where('document_type_enum', $companyModel->present()
                                                                                              ->getDocumentType('bank_document_status'))
                                                   ->whereHas('company', function($query) {
                                                       $query->where('bank_document_status', '!=', 3);
                                                   })->get();
        $companyAddressDocuments = $companyDocument->where('status', 3)->with('company')
                                                   ->where('document_type_enum', $companyModel->present()
                                                                                              ->getDocumentType('address_document_status'))
                                                   ->whereHas('company', function($query) {
                                                       $query->where('address_document_status', '!=', 3);
                                                   })->get();

        $companyContractDocuments = $companyDocument->where('status', 3)->with('company')
                                                    ->where('document_type_enum', $companyModel->present()
                                                                                               ->getDocumentType('contract_document_status'))
                                                    ->whereHas('company', function($query) {
                                                        $query->where('contract_document_status', '!=', 3);
                                                    })->get();

        foreach ($companyBankDocuments as $document) {
            $document->company->update(['bank_document_status' => $document->status]);
        }
        foreach ($companyAddressDocuments as $document) {
            $document->company->update(['address_document_status' => $document->status]);
        }
        foreach ($companyContractDocuments as $document) {
            $document->company->update(['contract_document_status' => $document->status]);
        }
    }
}


