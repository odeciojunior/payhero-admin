<?php

namespace Modules\PostBack\Http\Controllers;

use App\Entities\Company;
use App\Entities\HotZappIntegration;
use App\Entities\Plan;
use App\Entities\PlanSale;
use App\Entities\PostbackLog;
use App\Entities\Sale;
use App\Entities\ShopifyIntegration;
use App\Entities\Transaction;
use App\Entities\Transfer;
use App\Entities\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Checkout\Classes\MP;
use Modules\Core\Services\HotZappService;
use Modules\Core\Services\MercadoPagoService;
use Slince\Shopify\Client;
use Slince\Shopify\PublicAppCredential;

class PostBackMercadoPagoController extends Controller
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
        if (getenv('MERCADO_PAGO_PRODUCTION') == 'true') {
            try {
                $this->mp = new MP(getenv('MERCADO_PAGO_ACCESS_TOKEN_PRODUCTION'));
            } catch (Exception $e) {
                report($e);
            }
        } else {
            try {
                $this->mp = new MP(getenv('MERCADO_PAGO_ACCESS_TOKEN_SANDBOX'));
            } catch (Exception $e) {
                report($e);
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postBackListener(Request $request)
    {

        $requestData = $request->all();

        $postBackLogModel = new PostbackLog();

        $postBackLogModel->create([
                                      'origin'      => 4,
                                      'data'        => json_encode($requestData),
                                      'description' => 'mercado-pago',
                                  ]);

        if (isset($requestData['type']) && $requestData['type'] == 'payment') {

            $saleModel        = new Sale();
            $transactionModel = new Transaction();
            $companyModel     = new Company();
            $userModel        = new User();
            $planModel        = new Plan();
            $planSaleModel    = new PlanSale();

            $sale = $saleModel->where('gateway_id', $requestData['data']['id'])->first();

            if (empty($sale)) {
                Log::warning('VENDA NÃO ENCONTRADA!!!' . @$requestData['data']['id']);

                $postBackLogModel->create([
                                              'origin'      => 4,
                                              'data'        => json_encode($requestData),
                                              'description' => 'mercado-pago',
                                          ]);

                return response()->json(['message' => 'sale not found'], 200);
            }

            $paymentInfo = $this->mp->get('/v1/payments/' . @$requestData['data']['id']);

            if (isset($paymentInfo->error) && !empty($paymentInfo->error)) {
                Log::warning(MercadoPagoService::getErrorMessage(@$paymentInfo->error->causes[0]->code));
            }

            Log::warning('venda atualizada no mercado pago:  ' . print_r($paymentInfo, true));

            if ($paymentInfo->response->status == $sale->gateway_status) {
                return response()->json(['message' => 'success'], 200);
            }

            $transactions = $transactionModel->where('sale', $sale->id)->get();

            if ($paymentInfo->response->status == 'approved') {

                date_default_timezone_set('America/Sao_Paulo');

                $sale->update([
                                  'end_date'       => Carbon::now(),
                                  'gateway_status' => 'approved',
                                  'status'         => '1',
                              ]);

                foreach ($transactions as $transaction) {

                    if ($transaction->company != null) {

                        $company = $companyModel->find($transaction->company);

                        $user = $userModel->find($company['user_id']);

                        $transaction->update([
                                                 'status'            => 'approved',
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

                        $hotZappService->boletoPaid($sale, $plans);
                    }
                } catch (Exception $e) {
                    Log::warning('erro ao enviar notificação pro HotZapp na venda ' . $sale->id);
                    report($e);
                }
            } else {

                if ($paymentInfo->response->status == 'chargedback') {
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
                                      'gateway_status' => $paymentInfo->response->status,
                                  ]);

                    foreach ($transactions as $transaction) {
                        $transaction->update(['status' => $paymentInfo->response->status]);
                    }
                }
            }
        }

        return response()->json(['message' => 'success'], 200);
    }
}

