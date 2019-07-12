<?php

namespace Modules\PostBack\Http\Controllers;

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
use Vinkla\Hashids\Facades\Hashids;
use App\Entities\ShopifyIntegration;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Transportadoras\Kapsula;
use Modules\Core\Transportadoras\LiftGold;

class PostBackPagarmeController extends Controller {

    public function postBackListener(Request $request) {

        $requestData = $request->all();

        PostbackLog::create([
            'origin'      => 2,
            'data'        => json_encode($requestData),
            'description' => 'pagarme'
        ]);

        if(isset($requestData['event']) && $requestData['event'] = 'transaction_status_changed'){

            $sale = Sale::find(Hashids::decode($requestData['transaction']['metadata']['sale_id'])[0]);

            if($sale == null){
                Log::write('info', 'VENDA NÃO ENCONTRADA!!!' . Hashids::decode($requestData['transaction']['metadata']['sale_id'])[0]);
                return 'sucesso';
            }

            if($requestData['transaction']['status'] == $sale['gateway_status']){
                return 'sucesso';
            }

            $transactions = Transaction::where('sale',$sale->id)->get();

            if($requestData['transaction']['status'] == 'paid'){

                date_default_timezone_set('America/Sao_Paulo');

                $sale->update([
                    'end_date'       => Carbon::now(),
                    'gateway_status' => 'paid',
                    'status'         => '1'
                ]);

                foreach($transactions as $transaction){

                    if($transaction->company != null){

                        $company = Company::find($transaction->company);

                        $user = User::find($company['user_id']);

                        $transaction->update([
                            'status'            => 'paid',
                            'release_date'      => Carbon::now()->addDays($user['release_money_days'])->format('Y-m-d'),
                            'antecipation_date' => Carbon::now()->addDays($user['boleto_antecipation_money_days'])->format('Y-m-d'),
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
            else{
                foreach($transactions as $transaction){
                    $transaction->update(['status' => $requestData['transaction']['status']]);
                }
            }
        }
        return response()->json(['message' => 'success'], 200);
    }

}

