<?php

namespace Modules\PostBack\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Sale;
use App\Entities\Client;
use App\Entities\Company;
use App\Entities\Project;
use App\Entities\Delivery;
use App\Entities\PlanSale;
use App\Entities\Transfer;
use Illuminate\Http\Request;
use App\Entities\PostbackLog;
use App\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Checkout\Classes\MP;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Entities\HotZappIntegration;
use Modules\Core\Events\SaleApprovedEvent;
use Modules\Core\Services\MercadoPagoService;

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
            $projectModel     = new Project();
            $deliveryModel    = new Delivery();
            $clientModel      = new Client();

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

//            $paymentInfo = $this->mp->get('/v1/payments/' . @$requestData['data']['id']);

           /* if (isset($paymentInfo->error) && !empty($paymentInfo->error)) {
                Log::warning(MercadoPagoService::getErrorMessage(@$paymentInfo->error->causes[0]->code));
            }

            Log::warning('venda atualizada no mercado pago:  ' . print_r($paymentInfo, true));

            if ($paymentInfo->response->status == $sale->gateway_status) {
                return response()->json(['message' => 'success'], 200);
            }*/

            $transactions = $transactionModel->where('sale', $sale->id)->get();

            if ($requestData['data']['status'] == 'approved') {

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

                $plan     = $planModel->find($plansSale->plan);
                $project  = $projectModel->find($sale->project);
                $delivery = $deliveryModel->find($sale->delivery);
                $client   = $clientModel->find($sale->client);

                event(new SaleApprovedEvent($plan, $sale, $project, $delivery, $client));
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

