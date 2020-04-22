<?php

namespace Modules\Sales\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\UserProject;
use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Events\BilletRefundedEvent;
use Modules\Core\Events\SaleRefundedEvent;
use Modules\Core\Events\SaleRefundedPartialEvent;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\EmailService;
use Modules\Core\Services\ShopifyService;
use Modules\Sales\Exports\Reports\SaleReportExport;
use Modules\Sales\Http\Requests\SaleIndexRequest;
use Modules\Sales\Transformers\SalesResource;
use Modules\Sales\Transformers\TransactionResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SalesApiController
 * @package Modules\Sales\Http\Controllers
 */
class SalesApiController extends Controller
{
    /**
     * @param SaleIndexRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(SaleIndexRequest $request)
    {
        try {

            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todas as vendas');

            $saleService = new SaleService();

            $data = $request->all();

            $sales = $saleService->getPaginetedSales($data);

            return TransactionResource::collection($sales);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar vendas SalesApiController - index');
            report($e);

            return response()->json(['message' => 'Erro ao carregar vendas'], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse|SalesResource
     */
    public function show($id)
    {
        try {
            $saleModel = new Sale();

            activity()->on($saleModel)->tap(function(Activity $activity) use ($id) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($id));
            })->log('Visualizou detalhes da venda #' . $id);

            $saleService = new SaleService();

            if (isset($id)) {
                $sale = $saleService->getSaleWithDetails($id);

                return new SalesResource($sale);
            }

            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        } catch (Exception $e) {
            Log::warning('Erro ao mostrar detalhes da venda  SalesApiController - show');
            report($e);

            return response()->json(['error' => 'Erro ao exibir detalhes da venda'], 400);
        }
    }

    /**
     * @param SaleIndexRequest $request
     * @return JsonResponse
     */
    public function export(SaleIndexRequest $request)
    {
        try {
            $dataRequest = $request->all();

            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Exportou tabela ' . $dataRequest['format'] . ' de vendas');

            $user = auth()->user();

            $filename = 'sales_report_' . Hashids::encode($user->id) . '.' . $dataRequest['format'];

            (new SaleReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue('high');

            return response()->json(['message' => 'A exportação começou', 'email' => $user->email]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar gerar o arquivo Excel.'], 200);
        }
    }

    public function resume(SaleIndexRequest $request)
    {
        try {

            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela exibir resumo das venda ');

            $saleService = new SaleService();

            $data = $request->all();

            $resume = $saleService->getResume($data);

            return response()->json($resume);
        } catch (Exception $e) {
            Log::warning('Erro ao exibir resumo das venda  SalesApiController - resume');
            report($e);

            return response()->json(['error' => 'Erro ao exibir resumo das vendas'], 400);
        }
    }

    public function refund(Request $request, $saleId)
    {
        try {
            $checkoutService  = new CheckoutService();
            $saleService      = new SaleService();
            $saleModel        = new Sale();
            $companyModel     = new Company();
            $transactionModel = new Transaction();

            $sale = $saleModel->with('gateway', 'customer')->where('id', Hashids::connection('sale_id')
                                                                                ->decode($saleId))->first();

            $userCompanies = $companyModel->where('user_id', $sale->owner_id)->pluck('id');
            $transaction   = $transactionModel->where('sale_id', $sale->id)
                                              ->whereIn('company_id', $userCompanies)
                                              ->first();

            $partial    = boolval($request->input('partial'));
            $refundSale = intval(strval($sale->total_paid_value * 100));
            if (is_null($sale->interest_total_value)) {
                $saleService->updateInterestTotalValue($sale);
            }
            $totalWithoutInterest = $refundSale - $sale->interest_total_value;
            $refundValue          = preg_replace('/\D/', '', $request->input('refunded_value'));
            $partial              = ($totalWithoutInterest == $refundValue) ? false : $partial;
            $refundAmount         = ($partial == true) ? $refundValue : $refundSale;
            if (($refundAmount > $refundSale) || ($partial == true && $refundValue > ($totalWithoutInterest - 500))) {
                return response()->json(['message' => 'Valor inválido para estorno parcial.'], Response::HTTP_BAD_REQUEST);
            }

            $value = $transaction->company->balance - $refundAmount;

            if ($value < 0) {
                activity()->on($saleModel)->tap(function(Activity $activity) use ($saleId) {
                    $activity->log_name   = 'estorno';
                    $activity->subject_id = current(Hashids::connection('sale_id')->decode($saleId));
                })->log('Tentativa estorno transação: #' . $saleId);

                $pendingTransactions = $transactionModel->whereIn('company_id', $userCompanies)
                                                        ->where('status_enum', $transactionModel->present()
                                                                                                ->getStatusEnum('paid'))
                                                        ->whereDate('release_date', '>', now()->startOfDay())
                                                        ->select(DB::raw('sum( value ) as pending_balance'))
                                                        ->first();
                $pendingBalance      = intval($pendingTransactions->pending_balance);
                $valuePendingBalance = $pendingBalance - $refundAmount;

                if ($valuePendingBalance < -1000) {

                    return response()->json(['message' => 'Saldo insuficiente para realizar o estorno'], Response::HTTP_BAD_REQUEST);
                }
            }

            activity()->on($saleModel)->tap(function(Activity $activity) use ($saleId) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::connection('sale_id')->decode($saleId));
            })->log('Estorno transação: #' . $saleId);

            $partialValues = [];
            if ($partial == true) {
                $partialValues = $saleService->getValuesPartialRefund($sale, $refundAmount);
            }

            if (in_array($sale->gateway->name, ['zoop_sandbox', 'zoop_production', 'cielo_sandbox', 'cielo_production'])) {
                // Zoop e Cielo CancelPayment
                $result = $checkoutService->cancelPayment($sale, $refundAmount, $partialValues);
            } else {
                $result = $saleService->refund($saleId);
            }
            if ($result['status'] == 'success') {
                $sale->update([
                                  'date_refunded' => Carbon::now(),
                              ]);
                if ($partial == true) {
                    event(new SaleRefundedPartialEvent($sale));
                } else {
                    event(new SaleRefundedEvent($sale));
                }

                return response()->json(['message' => $result['message']], Response::HTTP_OK);
            } else {
                return response()->json(['message' => $result['message']], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar estornar venda  SalesApiController - cancelPayment');
            report($e);

            return response()->json(['message' => 'Erro ao tentar estornar venda.'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function refundBillet(Request $request, $saleId)
    {
        try {
            $saleModel        = new Sale();
            $userProjectModel = new UserProject();
            $transferModel    = new Transfer();
            $transactionModel = new Transaction();
            $saleId           = Hashids::connection('sale_id')->decode($saleId);
            if ($saleId) {
                $sale        = $saleModel->with('customer')->where('id', $saleId)->first();
                $userProject = $userProjectModel->with('company')->where('project_id', $sale->project_id)->first();
                if ($userProject->company->balance - preg_replace("/[^0-9]/", "", $sale->total_paid_value) < -1000) {

                    return response()->json(['message' => 'Saldo insuficiente para realizar o estorno'], Response::HTTP_BAD_REQUEST);
                }
                $transactionCompany = $transactionModel->where('sale_id', $sale->id)
                                                       ->where('company_id', $userProject->company_id)
                                                       ->first();
                if ($transactionCompany->status_enum == $transactionModel->present()->getStatusEnum('transfered')) {
                    $transactionCloudFox = $transactionModel->where('sale_id', $sale->id)
                                                            ->whereNull('company_id')
                                                            ->first();
                    //Transferencia de saída do usuário
                    $transferModel->create([
                                               'transaction_id' => null,
                                               'user_id'        => auth()->user()->account_owner_id,
                                               'company_id'     => $userProject->company_id,
                                               'customer_id'    => null,
                                               'value'          => $transactionCompany->value,
                                               'type_enum'      => $transferModel->present()->getTypeEnum('out'),
                                               'type'           => 'out',
                                               'reason'         => 'Estorno',
                                           ]);
                    //Taxa de estorno
                    $transferModel->create([
                                               'transaction_id' => null,
                                               'user_id'        => auth()->user()->account_owner_id,
                                               'company_id'     => $userProject->company_id,
                                               'customer_id'    => null,
                                               'value'          => $transactionCloudFox->value,
                                               'type_enum'      => $transferModel->present()->getTypeEnum('out'),
                                               'type'           => 'out',
                                               'reason'         => 'Taxa de estorno',
                                           ]);

                    $refundValue = $transactionCompany->value + $transactionCloudFox->value;
                    $userProject->company->update([
                                                      'balance' => $userProject->company->balance -= $refundValue,
                                                  ]);

                    //Transferencia de entrada do cliente
                    $transferModel->create([
                                               'transaction_id' => null,
                                               'user_id'        => auth()->user()->account_owner_id,
                                               'customer_id'    => $sale->customer_id,
                                               'company_id'     => null,
                                               'value'          => $refundValue,
                                               'type_enum'      => $transferModel->present()->getTypeEnum('in'),
                                               'type'           => 'in',
                                               'reason'         => 'Estorno de boleto',
                                           ]);
                    $sale->customer->update([
                                                'balance' => $sale->customer->balance + $refundValue,
                                            ]);
                    $sale->update([
                                      'status' => $sale->present()->getStatus('billet_refunded'),
                                  ]);
                    event(new BilletRefundedEvent($sale));
                } else if ($transactionCompany->status_enum == $transactionModel->present()->getStatusEnum('paid')) {
                    $transactionCompany->update([
                                                    'status_enum' => $transactionModel->present()->getStatusEnum('billet_refunded'),
                                                    'status'      => 'billet_refunded',
                                                ]);
                }

                return response()->json(['message' => 'Boleto estornado com sucesso'], Response::HTTP_OK);
            } else {
                return response()->json(['message' => 'Erro ao tentar estornar boleto'], Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar estornar boleto  SalesApiController - refundBillet');
            report($e);

            return response()->json(['error' => 'Erro ao tentar estornar boleto'], 400);
        }
    }

    /**
     * @param Request $request
     * @param $saleId
     * @return JsonResponse
     */
    public function newOrderShopify(Request $request, $saleId)
    {
        try {
            if (FoxUtils::isProduction()) {
                $result             = false;
                $saleModel          = new Sale();
                $sale               = $saleModel->find(Hashids::connection('sale_id')->decode($saleId))->first();
                $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();

                activity()->on($saleModel)->tap(function(Activity $activity) use ($saleId) {
                    $activity->log_name   = 'visualization';
                    $activity->subject_id = current(Hashids::connection('sale_id')->decode($saleId));
                })->log('Gerou nova ordem no shopify para transação: #' . $saleId);

                if (!FoxUtils::isEmpty($shopifyIntegration)) {
                    $shopifyService = new ShopifyService($shopifyIntegration->url_store, $shopifyIntegration->token);

                    $result = $shopifyService->newOrder($sale);
                    $shopifyService->saveSaleShopifyRequest();
                }
                if ($result['status'] == 'success') {
                    return response()->json(['message' => $result['message']], Response::HTTP_OK);
                } else {
                    return response()->json(['message' => $result['message']], Response::HTTP_BAD_REQUEST);
                }
            } else {
                return response()->json(['message' => 'Funcionalidade habilitada somente em produção =)'], Response::HTTP_OK);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar gerar ordem no Shopify SalesApiController - newOrderShopify');
            report($e);

            return response()->json(['message' => 'Erro ao tentar gerar ordem no Shopify.'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function saleProcess(Request $request)
    {
        try {

            $requestData = $request->all();

            $saleModel = new Sale();
            $planModel = new Plan();

            $plan = $planModel->find($requestData['plan_id']);
            $sale = $saleModel->with(['customer'])->find($requestData['sale_id']);

            activity()->on($saleModel)->tap(function(Activity $activity) use ($requestData) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::connection('sale_id')->decode($requestData['sale_id']));
            })->log('Processou boletos venda para transação: #' . $requestData['sale_id']);

            event(new BilletPaidEvent($plan, $sale, $sale->customer));

            return response()->json(['message' => 'success'], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::warning('Erro ao processar boletos venda  SalesApiController - saleProcess');
            report($e);

            return response()->json(['message' => 'Erro ao processar boleto.'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function saleReSendEmail(Request $request)
    {
        try {

            $saleModel = new Sale();
            $saleId    = current(Hashids::connection('sale_id')->decode($request->input('sale')));
            $sale      = $saleModel->with(['customer', 'project'])->find($saleId);

            activity()->on($saleModel)->tap(function(Activity $activity) use ($saleId, $request) {
                $activity->log_name   = 'created';
                $activity->subject_id = $saleId;
            })->log('Reenviou email para a venda: #' . $request->input('sale'));

            EmailService::clientSale(
                $sale->customer,
                $sale,
                $sale->project
            );

            return response()->json(['message' => 'Email enviado'], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::warning('Erro ao reenviar email da venda - saleReSendEmail');
            report($e);

            return response()->json(['message' => 'Erro ao reenviar email.'], Response::HTTP_BAD_REQUEST);
        }
    }
}
