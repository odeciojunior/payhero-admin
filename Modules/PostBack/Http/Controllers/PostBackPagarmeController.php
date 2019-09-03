<?php

namespace Modules\PostBack\Http\Controllers;

use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Sale;
use App\Entities\Company;
use App\Entities\PlanSale;
use App\Entities\Transfer;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use App\Entities\PostbackLog;
use App\Entities\Transaction;
use Modules\Core\Entities\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use App\Entities\HotZappIntegration;
use App\Entities\ShopifyIntegration;
use App\Entities\ConvertaxIntegration;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Services\HotZappService;

class PostBackPagarmeController extends Controller
{
    public function postBackListener(Request $request)
    {

        $requestData = $request->all();

        $postBackLogModel = new PostbackLog();

        $postBackLogModel->create([
                                      'origin'      => 2,
                                      'data'        => json_encode($requestData),
                                      'description' => 'pagarme',
                                  ]);

        if (isset($requestData['event']) && $requestData['event'] == 'transaction_status_changed') {

            $saleModel        = new Sale();
            $transactionModel = new Transaction();
            $companyModel     = new Company();
            $userModel        = new User();
            $planModel        = new Plan();
            $planSaleModel    = new PlanSale();

            $sale = $saleModel->find(Hashids::decode($requestData['transaction']['metadata']['sale_id'])[0]);

            if (empty($sale)) {
                Log::warning('VENDA NÃO ENCONTRADA!!!' . Hashids::decode($requestData['transaction']['metadata']['sale_id'])[0]);

                return response()->json(['message' => 'sale not found'], 200);
            }

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
                    } catch (\Exception $e) {
                        Log::warning('erro ao alterar estado do pedido no shopify com a venda ' . $sale->id);
                        report($e);
                    }
                }

                try {
                    $hotZappIntegrationModel = new HotZappIntegration();
                    $hotzappIntegration = $hotZappIntegrationModel->where('project_id', $plan->project)->first();

                    if (!empty($hotzappIntegration)) {
                        $hotZappService = new HotZappService($hotzappIntegration->link);
                        $hotZappService->newBoleto($sale);
                    }

                    $convertaxIntegrationModel = new ConvertaxIntegration();
                    $convertaxIntegration = $convertaxIntegrationModel->where('project_id', $plan->project)->first();

                    if (!empty($convertaxIntegration)) {
                        $hotZappService = new HotZappService($convertaxIntegration->link);
                        $hotZappService->newBoleto($sale);
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
}

