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
use App\Entities\Transaction;
use Illuminate\Http\Response;
use Modules\Core\HotZapp\HotZapp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Entities\ShopifyIntegration;
use Slince\Shopify\PublicAppCredential;
use Modules\Core\Transportadoras\Kapsula;
use Modules\Core\Transportadoras\LiftGold;

class PostBackPagarmeController extends Controller {

    public function postBackListener(Request $request) {

        $requestData = $request->all();

        Log::write('info', 'retorno do pagar.me : '. print_r($requestData, true));
 
        if(isset($requestData['event']) && $requestData['event'] = 'transaction_status_changed'){

            $sale = Sale::find(Hashids::decode($requestData['transaction']['metadata']['sale_id'])->first());

            Log::write('info', 'alterando dados da venda : '. $sale['id']);

            if($sale == null){
                Log::write('info', 'VENDA NÃO ENCONTRADA!!!');
                return 'sucesso';
            }

            if($requestData['transaction']['status'] == $sale['gateway_status']){
                return 'sucesso';
            }

            $transactions = Transaction::where('sale',$sale->id)->get()->toArray();

            if($requestData['transaction']['status'] == 'paid'){

                date_default_timezone_set('America/Sao_Paulo');

                $sale->update([
                    'end_date'       => Carbon::now(),
                    'gateway_status' => 'paid',
                ]);

                foreach($transactions as $t){

                    $transaction = Transaction::find($t['id']);

                    if($transaction['company'] != null){

                        $company = Company::find($transaction['company']);

                        $user = User::find($company['user']);

                        $transaction->update([
                            'status'       => 'paid',
                            'release_date' => Carbon::now()->addDays($user['antecipation_days'])->format('Y-m-d')
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
                        Log::write('info',  print_r($e, true) );
                    }

                }

            }
            else{
                foreach($transactions as $transaction){
                    Transaction::find($transaction['id'])->update('status',$requestData['transaction']['status']);
                }
            }
        }
        return 'sucesso';
    }

}

