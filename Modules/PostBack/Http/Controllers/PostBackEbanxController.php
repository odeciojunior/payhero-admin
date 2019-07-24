<?php

namespace Modules\PostBack\Http\Controllers;

use \Ebanx\Config;
use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Sale;
use App\Entities\User;
use App\Entities\Company;
use App\Entities\PlanSale;
use Slince\Shopify\Client;
use Illuminate\Http\Request;
use App\Entities\PostbackLog;
use App\Entities\Transaction;
use Illuminate\Http\Response;
use Modules\Core\HotZapp\HotZapp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Entities\ShopifyIntegration;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Transportadoras\Kapsula;
use Modules\Core\Transportadoras\LiftGold;

class PostBackEbanxController extends Controller {

    public function postBackListener(Request $request){

        $requestData = $request->all();

        $postbackLogModel = new PostbackLog();

        $postbackLogModel->create([
            'origin'      => 1,
            'data'        => json_encode($requestData),
            'description' => 'ebanx'
        ]);

        Config::set([
            'integrationKey' => 'live_ik_mTLNBPdU-RmtpVW6FTF0Ug',
            'testMode'       => false
        ]);

        $response = \Ebanx\Ebanx::doQuery([
            'hash' => $requestData['hash_codes']
        ]);

        if(!isset($response->payment->status)){
            Log::write('info', 'Erro com response do ebanx ' . print_r($response, true));
            return response()->json(['message' => 'success'], 200);
        }

        $saleModel = new Sale();

        $sale = $saleModel->where('gateway_id',$requestData['hash_codes'])->first();

        if(!$sale){
            Log::write('info', 'Venda não encontrada no retorno do Ebanx com código ' . $requestData['hash_codes']);
            return response()->json(['message' => 'sale not found'], 200);
        }

        if($response->payment->status != $sale->gateway_status){

            $sale->update([
                'gateway_status' => $response->payment->status,
            ]);

            $transactionModel = new Transaction();
            $companyModel     = new Company();
            $userModel        = new User();

            $transactions = $transactionModel->where('sale',$sale->id)->get();

            if($response->payment->status == 'CA'){
 
                $sale->update([
                    'status' => '3'
                ]);

                foreach($transactions as $transaction){
                    $transaction->update([
                        'status' => 'canceled'
                    ]);
                }
            }

            else if($response->payment->status == 'CO'){

                date_default_timezone_set('America/Sao_Paulo');

                $sale->update([
                    'end_date'       => \Carbon\Carbon::now(),
                    'gateway_status' => 'CO',
                    'status'         => '1'
                ]);

                foreach($transactions as $transaction){

                    if($transaction->company != null){

                        $company = $companyModel->find($transaction->company);

                        $user = $userModel->find($company['user_id']);

                        $transaction->update([
                            'status'            => 'paid',
                            'release_date'      => Carbon::now()->addDays($user->release_money_days)->format('Y-m-d'),
                            'antecipation_date' => Carbon::now()->addDays($user->boleto_antecipation_money_days)->format('Y-m-d'),
                        ]);
                    }
                    else{
                        $transaction->update([
                            'status' => 'paid',
                        ]);
                    }
                }

                if($sale['shopify_order'] != ''){

                    $planSaleModel           = new PlanSale();
                    $planModel               = new Plan();
                    $shopifyIntegrationModel = new ShopifyIntegration();

                    $plansSale = $planSaleModel->where('sale', $sale['id'])->first();

                    $plan = $planModel->find($plansSale->plan);

                    $shopifyIntegration = $shopifyIntegrationModel->where('project',$plan['project'])->first();

                    try{
                        $credential = new PublicAppCredential($shopifyIntegration['token']);

                        $client = new Client($credential, $shopifyIntegration['url_store'], [
                            'metaCacheDir' => './tmp'
                        ]);

                        $client->getTransactionManager()->create($sale['shopify_order'],[
                            "kind"      => "capture",
                        ]);

                    }
                    catch(\Exception $e){
                        Log::write('info', 'erro ao alterar estado do pedido no shopify com a venda '.$sale['id']);
                        report($e);
                    }

                }
            }

        }

        return response()->json(['message' => 'success'], 200);
    }

}


