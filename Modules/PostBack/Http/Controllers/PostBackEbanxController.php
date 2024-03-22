<?php

namespace Modules\PostBack\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\Shopify\Client;
use Modules\Core\Services\Shopify\TransactionService;
use Vinkla\Hashids\Facades\Hashids;

class PostBackEbanxController extends Controller
{
    public function postBackListener(Request $request)
    {
        $saleModel = new Sale();
        $postbackLogModel = new PostbackLog();

        $requestData = $request->all();

        $postbackLogModel->create([
            "origin" => 1,
            "data" => json_encode($requestData),
            "description" => "ebanx",
        ]);

        Config::set([
            "integrationKey" => "live_ik_mTLNBPdU-RmtpVW6FTF0Ug",
            "testMode" => false,
        ]);

        $sale = $saleModel->where("gateway_transaction_id", $requestData["hash_codes"])->first();

        if (!$sale) {
            Log::warning("Venda não encontrada no retorno do Ebanx com código " . $requestData["hash_codes"]);

            return response()->json(["message" => "sale not found"], 200);
        }

        if (!empty($request->input("notification_type")) && $request->input("notification_type") == "chargeback") {
            $sale->update([
                "status" => 4,
                "gateway_status" => "chargedback",
            ]);
        }

        $response = \Ebanx\Ebanx::doQuery([
            "hash" => $requestData["hash_codes"],
        ]);

        if (!isset($response->payment->status)) {
            Log::warning("Erro com response do ebanx " . print_r($response, true));

            return response()->json(["message" => "success"], 200);
        }

        if ($response->payment->status != $sale->gateway_status) {
            $sale->update([
                "gateway_status" => $response->payment->status,
            ]);

            $transactionModel = new Transaction();
            $companyModel = new Company();
            $userModel = new User();

            $transactions = $transactionModel->where("sale_id", $sale->id)->get();

            if ($response->payment->status == "CA") {
                if ($sale->payment_method == 2) {
                    $sale->update([
                        "status" => 5,
                    ]);
                } else {
                    $sale->update([
                        "status" => 3,
                    ]);
                }

                foreach ($transactions as $transaction) {
                    $transaction->update([
                        "status" => "canceled",
                    ]);
                }
            } elseif ($response->payment->status == "CO") {
                date_default_timezone_set("America/Sao_Paulo");

                $sale->update([
                    "end_date" => \Carbon\Carbon::now(),
                    "gateway_status" => "CO",
                    "status" => "1",
                ]);

                foreach ($transactions as $transaction) {
                    if ($transaction->company != null) {
                        $company = $companyModel->find($transaction->company_id);

                        $user = $userModel->find($company["user_id"]);

                        $transaction->update([
                            "status" => "paid",
                            "release_date" => Carbon::now()
                                ->addDays($user->release_money_days)
                                ->format("Y-m-d"),
                        ]);
                    } else {
                        $transaction->update([
                            "status" => "paid",
                        ]);
                    }
                }

                if ($sale["shopify_order"] != "") {
                    $planSaleModel = new PlanSale();
                    $planModel = new Plan();
                    $shopifyIntegrationModel = new ShopifyIntegration();

                    $plansSale = $planSaleModel->where("sale_id", $sale["id"])->first();

                    $plan = $planModel->find($plansSale->plan_id);

                    $shopifyIntegration = $shopifyIntegrationModel->where("project_id", $plan["project_id"])->first();

                    try {
                        $client = new Client($shopifyIntegration["url_store"], $shopifyIntegration["token"]);
                        $transactionService = new TransactionService($client);

                        $transactionUpdate = [
                            "kind" => "sale",
                            "source" => "external",
                            "gateway" => "Azcend",
                            "authorization" => Hashids::connection("sale_id")->encode($sale->id),
                        ];

                        $transactionService->create($sale->shopify_order, $transactionUpdate);
                    } catch (\Exception $e) {
                        Log::warning("erro ao alterar estado do pedido no shopify com a venda " . $sale["id"]);
                        report($e);
                    }
                }
            }
        }

        return response()->json(["message" => "success"], 200);
    }
}
