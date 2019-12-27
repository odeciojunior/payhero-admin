<?php

namespace Modules\Sales\Http\Controllers;

use App\Services\FoxUtilsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Events\BilletPaidEvent;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\ShopifyService;
use Modules\Sales\Exports\Reports\SaleReportExport;
use Modules\Sales\Http\Requests\SaleIndexRequest;
use Modules\Sales\Transformers\SalesResource;
use Modules\Sales\Transformers\TransactionResource;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

            $user = auth()->user();

            $filename = 'sales_report_' . Hashids::encode($user->id) . '.' . $dataRequest['format'];

            (new SaleReportExport($dataRequest, auth()->user(), $filename))->queue($filename);

            return response()->json(['message' => 'A exportação começou']);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar gerar o arquivo Excel.'], 200);
        }
    }

    public function resume(SaleIndexRequest $request)
    {
        try {
            $saleService = new SaleService();

            $data = $request->all();

            $transactions = $saleService->getAllSales($data);

            if ($transactions->count()) {
                $resume = $transactions->reduce(function($carry, $item) use ($saleService) {
                    //quantidade de vendas
                    $carry['total_sales'] += 1;
                    //cria um item no array pra cada moeda inclusa nas vendas
                    $item->currency         = $item->currency ?? 'real';
                    $carry[$item->currency] = $carry[$item->currency] ?? ['comission' => 0, 'total' => 0];
                    //comissao
                    $carry[$item->currency]['comission'] += in_array($item->status, ['paid', 'transfered', 'anticipated']) ? (floatval($item->value) / 100) : 0;
                    //calcula o total
                    $total            = $item->sale->sub_total;
                    $total            += $item->sale->shipment_value;
                    $shopify_discount = floatval($item->sale->shopify_discount) / 100;
                    if ($shopify_discount > 0) {
                        $total -= $shopify_discount;
                    }
                    if ($item->sale->dolar_quotation != 0) {
                        $iof   = preg_replace('/[^0-9]/', '', $item->sale->iof);
                        $iof   = substr_replace($iof, '.', strlen($iof) - 2, 0);
                        $total += floatval($iof);
                    }
                    $carry[$item->currency]['total'] += $total;

                    return $carry;
                }, ['total_sales' => 0]);

                //formata os valores
                foreach ($resume as &$item) {
                    if (is_array($item)) {
                        foreach ($item as &$value) {
                            $value = number_format($value, 2, ',', '.');
                        }
                    }
                }

                return response()->json($resume);
            } else {
                return response()->json([]);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao exibir resumo das venda  SalesApiController - resume');
            report($e);

            return response()->json(['error' => 'Erro ao exibir resumo das vendas'], 400);
        }
    }

    public function refund(Request $request, $saleId)
    {
        try {
            $checkoutService = new CheckoutService();
            $saleService     = new SaleService();
            $saleModel       = new Sale();
            $sale            = $saleModel->with('gateway')->where('id', Hashids::connection('sale_id')->decode($saleId))
                                         ->first();
            $refundAmount    = Str::replaceFirst(',', '', Str::replaceFirst('.', '', Str::replaceFirst('R$ ', '', $sale->total_paid_value)));
            if (in_array($sale->gateway->name, ['zoop_sandbox', 'zoop_production', 'cielo_sandbox', 'cielo_production'])) {
                // Zoop e Cielo CancelPayment
                $result = $checkoutService->cancelPayment($sale, $refundAmount);
            } else {
                $result = $saleService->refund($saleId);
            }
            if ($result['status'] == 'success') {
                $sale->update([
                                  'date_refunded' => Carbon::now(),
                              ]);

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

    public function newOrderShopify(Request $request, $saleId)
    {
        try {
            if (FoxUtils::isProduction()) {
                $result             = false;
                $saleModel          = new Sale();
                $sale               = $saleModel->find(Hashids::connection('sale_id')->decode($saleId))->first();
                $shopifyIntegration = ShopifyIntegration::where('project_id', $sale->project_id)->first();
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
            $sale = $saleModel->with(['client'])->find($requestData['sale_id']);

            event(new BilletPaidEvent($plan, $sale, $sale->client));

            return response()->json(['message' => 'success'], Response::HTTP_OK);
        } catch (Exception $e) {
            Log::warning('Erro ao processar boletos venda  SalesApiController - saleProcess');
            report($e);

            return response()->json(['message' => 'Erro ao processar boleto.'], Response::HTTP_BAD_REQUEST);
        }
    }
}
