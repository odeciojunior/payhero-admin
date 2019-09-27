<?php

namespace Modules\SalesRecovery\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Services\PagarmeService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\SaleService;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\SalesRecoveryService;
use Modules\SalesRecovery\Transformers\SalesRecoveryIndexResourceTransformer;
use Modules\SalesRecovery\Transformers\SalesRecoverydetailsResourceTransformer;
use Modules\SalesRecovery\Transformers\SalesRecoveryCartAbandonedDetailsResourceTransformer;

/**
 * Class SalesRecoveryApiController
 * @package Modules\SalesRecovery\Http\Controllers
 */
class SalesRecoveryApiController extends Controller
{
    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $projectService = new ProjectService();

            $projects = $projectService->getMyProjects();
            if (!empty($projects)) {
                return SalesRecoveryIndexResourceTransformer::collection($projects);
            } else {
                return response()->json([
                                            'message' => 'Erro ao listar projetos, tente novamente mais tarde',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao listar projetos, tente novamente mais tarde');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao listar projetos, tente novamente mais tarde',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection|string
     */
    public function getRecoveryData(Request $request)
    {
        try {
            $salesRecoveryService = new SalesRecoveryService();

            $requestValidate = Validator::make($request->all(), [
                'project'     => 'required|string',
                'type'        => 'required|string',
                'start_date'  => 'nullable',
                'end_date'    => 'nullable',
                'client_name' => 'nullable|string',
            ]);

            if ($requestValidate->fails()) {
                return response()->json([
                                            'message' => 'Erro ao listar projetos, tente novamente mais tarde',
                                        ], 400);
            } else {
                $projectId = null;
                if ($request->has('project') && !empty($request->input('project'))) {
                    $projectId = current(Hashids::decode($request->input('project')));
                }

                $client = null;
                if ($request->has('client_name') && !empty($request->input('client_name'))) {
                    $client = $request->input('client_name');
                }

                $endDate = null;
                if ($request->has('end_date') && !empty($request->input('end_date'))) {
                    $endDate = date('Y-m-d', strtotime($request->input('end_date') . ' + 1 day'));
                }

                $startDate = null;
                if ($request->has('start_date') && !empty($request->input('start_date'))) {
                    $startDate = date('Y-m-d', strtotime($request->input('start_date')));
                }

                return $salesRecoveryService->verifyType($request->input('type'), $projectId, $startDate, $endDate, $client);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados de recuperação de vendas');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao listar projetos, tente novamente mais tarde',
                                    ], 400);
        }
    }

    /**
     * @param Request $request
     * @return SalesRecoveryCartAbandonedDetailsResourceTransformer|SalesRecoverydetailsResourceTransformer
     */
    public function getDetails(Request $request)
    {
        try {
            $saleModel            = new Sale();
            $checkoutModel        = new Checkout();
            $salesRecoveryService = new SalesRecoveryService();

            if ($request->has('checkout') && !empty($request->input('checkout'))) {
                $saleId = current(Hashids::decode($request->input('checkout')));
                $sale   = $saleModel->find($saleId);
                if (!empty($sale)) {

                    return SalesRecoverydetailsResourceTransformer::make($salesRecoveryService->getSalesCartOrBoletoDetails($sale));
                } else {
                    $checkout = $checkoutModel->find($saleId);
                    if (!empty($checkout)) {
                        return SalesRecoveryCartAbandonedDetailsResourceTransformer::make($salesRecoveryService->getSalesCheckoutDetails($checkout));
                    } else {
                        return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde'], 400);
                    }
                }
            } else {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar detalhes do carrinho abandonado');
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde'], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function regenerateBoleto(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'saleId' => 'required|string',
                'date'   => 'required|string',
            ]);
            if ($validator->fails()) {
                return response()->json([
                                            'message' => "Preencha os dados corretamente",
                                        ], 400);
            } else {

                $saleModel   = new Sale();
                $saleService = new SaleService();

                $sale = $saleModel->find(current(Hashids::decode($request->input('saleId'))));

                if (!empty($sale)) {
                    $totalPaidValue = $saleService->getSubTotal($sale);
                    $shippingPrice  = preg_replace("/[^0-9]/", "", $sale->shipment_value);
                    $pagarmeService = new PagarmeService($sale, $totalPaidValue, $shippingPrice);

                    $boletoRegenerated = $pagarmeService->boletoPayment($request->input('date'));
                    if ($boletoRegenerated['status'] == 'success') {
                        $message = 'Boleto regenerado com sucesso';
                        $status  = 200;
                    } else {
                        $message = 'Ocorreu um erro tente novamente mais tarde';
                        $status  = 400;
                    }

                    return response()->json([
                                                'message' => $message,
                                            ], $status);
                } else {

                    return response()->json([
                                                'message' => "Ocorreu um erro, tente novamente mais tarde",
                                            ], 400);
                }
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar regenerar Boleto (saleRecoveryApiController - regenerateSale)');
            report($e);

            return response()->json([
                                        'message' => "Ocorreu um erro, tente novamente mais tarde",
                                    ], 400);
        }
    }
}
