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

        date_default_timezone_set('America/Sao_Paulo');

        $requestData = $request->all();

        PostbackLog::create([
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
            return 'success';
        }

        $sale = Sale::where('gateway_id',$requestData['hash_codes'])->first();

        if(!$sale){
            Log::write('info', 'Venda não encontrada no retorno do Ebanx com código ' . $requestData['hash_codes']);
            return 'success';
        }

        if($response->payment->status != $sale->gateway_status){

            $sale->update([
                'gateway_status' => $response->payment->status,
            ]);

            $transactions = Transaction::where('sale',$sale->id)->get()->toArray();

            if($response->payment->status == 'CA'){
 
                $sale->update([
                    'status' => '3'
                ]);

                foreach($transactions as $transaction){
                    Transaction::find($transaction['id'])->update('status','cancelada');
                }
            }

            else if($response->payment->status == 'CO'){

                date_default_timezone_set('America/Sao_Paulo');

                $sale->update([
                    'end_date'       => \Carbon\Carbon::now(),
                    'gateway_status' => 'CO',
                    'status'         => '1'
                ]);

                foreach($transactions as $t){

                    $transaction = Transaction::find($t['id']);

                    if($transaction['company'] != null){

                        $company = Company::find($transaction['company']);

                        $user = User::find($company['user_id']);

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

                    $plansSale = PlanSale::where('sale', $sale['id'])->first();

                    $plan = Plan::find($plansSale->plan);

                    $shopifyIntegration = ShopifyIntegration::where('project',$plan['project'])->first();

                    try{
                        $credential = new PublicAppCredential($shopifyIntegration['token']);

                        $client = new Client($credential, $shopifyIntegration['url_store'], [
                            'metaCacheDir' => './tmp'
                        ]);

                        $transaction = $client->getTransactionManager()->create($sale['shopify_order'],[
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

        return 'success';
    }

}


