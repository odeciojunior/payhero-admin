<?php

namespace Modules\PostBack\Http\Controllers;

use Modules\Core\Entities\Company;
use Modules\Core\Entities\ConvertaxIntegration;
use Modules\Core\Entities\HotzappIntegration;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\HotZappService;
use Modules\Core\Services\ActiveCampaignService;
use Modules\Core\Entities\User;
use Modules\Core\Events\BilletPaidEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Modules\Core\Services\ShopifyService;
use Slince\Shopify\PublicAppCredential;
use Slince\Shopify\Client;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use Exception;

/**
 * Class PostBackPagarmeController
 * @package Modules\PostBack\Http\Controllers
 */
class PostBackPagarmeController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postBackListener(Request $request)
    {
        $requestData    = $request->all();

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

            $sale               = $saleModel->find(Hashids::decode($requestData['transaction']['metadata']['sale_id'])[0]);
            $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();
            $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

            if (empty($sale)) {
                Log::warning('VENDA NÃO ENCONTRADA!!!' . Hashids::decode($requestData['transaction']['metadata']['sale_id'])[0]);

                return response()->json(['message' => 'sale not found'], 200);
            }

            if ($requestData['transaction']['status'] == $sale->gateway_status) {
                return response()->json(['message' => 'success'], 200);
            }

            $transactions = $transactionModel->where('sale_id', $sale->id)->get();

            if ($requestData['transaction']['status'] == 'paid') {

                date_default_timezone_set('America/Sao_Paulo');

                $sale->update([
                                  'end_date'       => Carbon::now(),
                                  'gateway_status' => 'paid',
                                  'status'         => '1',
                              ]);

                foreach ($transactions as $transaction) {

                    if ($transaction->company != null) {

                        $company = $companyModel->find($transaction->company_id);

                        $user = $userModel->find($company['user_id']);

                        $transaction->update([
                                                 'status'            => 'paid',
                                                 'release_date'      => Carbon::now()
                                                                              ->addDays($user['boleto_release_money_days'])
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

                $plansSale = $planSaleModel->where('sale_id', $sale->id)->first();

                $plan = $planModel->find($plansSale->plan_id);

                if (!FoxUtils::isEmpty($sale->shopify_order)) {

                    $shopifyIntegrationModel = new ShopifyIntegration();

                    $shopifyIntegration = $shopifyIntegrationModel->where('project_id', $plan->project_id)->first();

                    try {
                        $credential = new PublicAppCredential($shopifyIntegration['token']);

                        $client            = new Client($credential, $shopifyIntegration['url_store'], [
                            'metaCacheDir' => './tmp',
                        ]);
                        $transactionUpdate = [
                            "kind"   => "sale",
                            "source" => "external",
                        ];
                        if ($sale->payment_method == 2) {
                            $transactionUpdate['gateway'] = 'Boleto';
                        }
                        $client->getTransactionManager()->create($sale->shopify_order, $transactionUpdate);
                    } catch (Exception $e) {
                        Log::warning('erro ao alterar estado do pedido no shopify com a venda ' . $sale->id);
                        report($e);
                    }
                }

                $sale->load('client');
                event(new BilletPaidEvent($plan, $sale, $sale->client));

                // try {
                //     $hotZappIntegrationModel = new HotZappIntegration();
                //     $hotzappIntegration      = $hotZappIntegrationModel->where('project_id', $plan->project_id)
                //                                                        ->first();

                //     if (!empty($hotzappIntegration)) {
                //         $hotZappService = new HotZappService($hotzappIntegration->link);
                //         $hotZappService->boletoPaid($sale);
                //     }

                //     $convertaxIntegrationModel = new ConvertaxIntegration();
                //     $convertaxIntegration      = $convertaxIntegrationModel->where('project_id', $plan->project_id)
                //                                                            ->first();

                //     if (!empty($convertaxIntegration)) {
                //         $hotZappService = new HotZappService($convertaxIntegration->link);
                //         $hotZappService->boletoPaid($sale);
                //     }
                // } catch (Exception $e) {
                //     Log::warning('erro ao enviar notificação pro HotZapp na venda ' . $sale->id);
                //     report($e);
                // }

                // try {
                //     $activeCampaignService = new ActiveCampaignService();
                //     $sale->load('client');
                //     // $saleId, $eventSale, $name, $phone, $email, $projectId
                //     $activeCampaignService->execute($sale->id, 2, $sale->client->name, $sale->client->telephone, $sale->client->email, $sale->project_id, 'sale');

                // } catch (Exception $e) {
                //     Log::warning('Erro ao enviar lead para ActiveCampaign na venda ' . $sale->id);
                //     report($e);
                // }
            } else if ($requestData['transaction']['status'] == 'chargedback') {
                $sale->update([
                                  'gateway_status' => 'chargedback',
                                  'status'         => '4',
                              ]);
                if (!FoxUtils::isEmpty($sale->shopify_order) && !FoxUtils::isEmpty($shopifyIntegration)) {

                    $shopifyService->refundOrder($shopifyIntegration, $sale);
                    $shopifyService->saveSaleShopifyRequest();
                }
                $transferModel = new Transfer();

                foreach ($transactions as $transaction) {

                    if ($transaction->status == 'transfered') {
                        $company = $companyModel->find($transaction->company_id);

                        $transferModel->create([
                                                   'transaction_id' => $transaction->id,
                                                   'user_id'        => $company->user_id,
                                                   'value'          => $transaction->value,
                                                   'type'           => 'out',
                                                   'reason'         => 'chargedback',
                                                   'company_id'     => $company->id,
                                               ]);

                        $company->update([
                                             'balance' => $company->balance -= $transaction->value,
                                         ]);
                    } else if ($transaction->status == 'anticipated') {

                        $company = $companyModel->find($transaction->company_id);

                        $transferModel->create([
                                                   'transaction_id' => $transaction->id,
                                                   'user_id'        => $company->user_id,
                                                   'value'          => $transaction->antecipable_value,
                                                   'type'           => 'out',
                                                   'reason'         => 'chargedback',
                                                   'company_id'     => $company->id,
                                               ]);

                        $company->update([
                                             'balance' => $company->balance -= $transaction->value,
                                         ]);
                    }

                    $transaction->update([
                                             'status' => 'chargedback',
                                         ]);
                }
            } else if ($requestData['transaction']['status'] == 'refunded') {
                $sale->update([
                                  'gateway_status' => 'refunded',
                                  'status'         => '7',
                              ]);
                if (!FoxUtils::isEmpty($sale->shopify_order) && !FoxUtils::isEmpty($shopifyIntegration)) {

                    $shopifyService->refundOrder($shopifyIntegration, $sale);
                    $shopifyService->saveSaleShopifyRequest();
                }
                $transferModel = new Transfer();

                foreach ($transactions as $transaction) {

                    if ($transaction->status == 'transfered') {
                        $company = $companyModel->find($transaction->company_id);

                        $transferModel->create([
                                                   'transaction_id' => $transaction->id,
                                                   'user_id'        => $company->user_id,
                                                   'value'          => $transaction->value,
                                                   'type'           => 'out',
                                                   'reason'         => 'refunded',
                                                   'company_id'     => $company->id,
                                               ]);

                        $company->update([
                                             'balance' => $company->balance -= $transaction->value,
                                         ]);
                    } else if ($transaction->status == 'anticipated') {

                        $company = $companyModel->find($transaction->company_id);

                        $transferModel->create([
                                                   'transaction_id' => $transaction->id,
                                                   'user_id'        => $company->user_id,
                                                   'value'          => $transaction->antecipable_value,
                                                   'type'           => 'out',
                                                   'reason'         => 'chargedback',
                                                   'company_id'     => $company->id,
                                               ]);

                        $company->update([
                                             'balance' => $company->balance -= $transaction->value,
                                         ]);
                    }

                    $transaction->update([
                                             'status' => 'refunded',
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

        return response()->json(['message' => 'success'], 200);
    }
}

