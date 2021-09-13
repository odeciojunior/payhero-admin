<?php

namespace Modules\SalesRecovery\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\PagarmeService;
use Modules\Core\Services\ProjectService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\SalesRecoveryService;
use Modules\Sales\Exports\Reports\AbandonedCartReportExport;
use Modules\Sales\Exports\Reports\BilletExpiredReportExport;
use Modules\Sales\Exports\Reports\CardRefusedReportExport;
use Modules\Sales\Exports\Reports\PixExpiredReportExport;
use Modules\SalesRecovery\Transformers\SalesRecoveryCardRefusedResource;
use Modules\SalesRecovery\Transformers\SalesRecoveryCartAbandonedDetailsResourceTransformer;
use Modules\SalesRecovery\Transformers\SalesRecoverydetailsResourceTransformer;
use Modules\SalesRecovery\Transformers\SalesRecoveryIndexResourceTransformer;
use Vinkla\Hashids\Facades\Hashids;

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
            $projectModel   = new Project();

            $projectStatus = [
                $projectModel->present()->getStatus('active'), $projectModel->present()->getStatus('disabled'),
            ];

            $projects = $projectService->getUserProjects(true, $projectStatus);
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

    public function getCartRefused(Request $request)
    {
        try {
            $data                 = $request->all();
            $salesRecoveryService = new SalesRecoveryService();

            $projectId = "";
            if (!empty($data['project'])) {
                $projectId = current(Hashids::decode($data['project']));
            }

            $client = null;
            if (!empty($data['client'])) {
                $client = $data['client'];
            }

            $clientDocument = null;
            if (!empty($data['client_document'])) {
                $clientDocument = $data['client_document'];
            }

            $plan = null;
            if (!empty($data['plan'])) {
                $plan = $data['plan'];
            }

            $dateStart = null;
            $dateEnd   = null;

            $dateRange = FoxUtils::validateDateRange($data["date_range"]);
            if (!empty($data["date_type"]) && $dateRange) {
                $dateStart = $dateRange[0] . ' 00:00:00';
                $dateEnd   = $dateRange[1] . ' 23:59:59';
            }

            $paymentMethod = (new Sale())->present()->getPaymentType('credit_card');
            $status        = [3];

            $sales = $salesRecoveryService->getSaleExpiredOrRefused($paymentMethod, $status, $projectId, $dateStart, $dateEnd, $client, $clientDocument, $plan);

            return SalesRecoveryCardRefusedResource::collection($sales);
        } catch (Exception $e) {
            Log::warning('Erro buscar dados cartão recusado, SalesRecoveryApiController - getCartRefused');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, tente novamente mais tarde',
            ]);
        }
    }

    public function getBoletoOverdue(Request $request)
    {
        $data                 = $request->all();
        $salesRecoveryService = new SalesRecoveryService();

        $projectId = "all";
        if (!empty($data['project'])) {
            $projectId = current(Hashids::decode($data['project']));
        }

        $client = null;
        if (!empty($data['client'])) {
            $client = $data['client'];
        }

        $clientDocument = null;
        if (!empty($data['client_document'])) {
            $clientDocument = $data['client_document'];
        }

        $plan = null;
        if (!empty($data['plan'])) {
            $plan = $data['plan'];
        }

        $dateStart = null;
        $dateEnd   = null;

        $dateRange = FoxUtils::validateDateRange($data["date_range"]);
        if (!empty($data["date_type"]) && $dateRange) {
            $dateStart = $dateRange[0] . ' 00:00:00';
            $dateEnd   = $dateRange[1] . ' 23:59:59';
        }

        $paymentMethod = (new Sale())->present()->getPaymentType('boleto');
        $status        = [5];

        $sales = $salesRecoveryService->getSaleExpiredOrRefused($paymentMethod, $status, $projectId, $dateStart, $dateEnd, $client, $clientDocument, $plan);

        return SalesRecoveryCardRefusedResource::collection($sales);
    }

    /**
     * @param Request $request
     * @return JsonResponse|SalesRecoveryCartAbandonedDetailsResourceTransformer|SalesRecoverydetailsResourceTransformer
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
                    $totalPaidValue = (int)preg_replace("/[^0-9]/", "", $sale->sub_total) - (int)$sale->automatic_discount;
                    $shippingPrice  = (int)preg_replace("/[^0-9]/", "", $sale->shipment_value);

                    if (!empty($request->input('discountValue'))) {

                        if ($request->discountType == 'percentage') {
                            $discount       = (int)($totalPaidValue * (((int)preg_replace("/[^0-9]/", "", $request->input('discountValue'))) / 100));
                            $totalPaidValue -= $discount;
                        } else {
                            $discount       = (int)(preg_replace("/[^0-9]/", "", $request->input('discountValue')));
                            $totalPaidValue -= $discount;
                        }

                        $sale->update([
                            'shopify_discount' => $discount,
                        ]);
                    }

                    $dueDate = $request->input('date');
                    if (Carbon::parse($dueDate)->isWeekend()) {
                        $dueDate = Carbon::parse($dueDate)->nextWeekday()->format('Y-m-d');
                    }
                    //                    if (in_array($sale->gateway_id, [7])) {
                    $checkoutService   = new CheckoutService();
                    $boletoRegenerated = $checkoutService->regenerateBillet(Hashids::connection('sale_id')
                    ->encode($sale->id), ($totalPaidValue + $shippingPrice), $dueDate);
                    //                    } else {
                    //                        $pagarmeService = new PagarmeService($sale, $totalPaidValue, $shippingPrice);
                    //
                    //                        $boletoRegenerated = $pagarmeService->boletoPayment($dueDate);
                    //                    }
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

    public function export(Request $request)
    {
        try {
            $dataRequest = $request->all();
            $user        = auth()->user();

            if ($dataRequest['recovery_type'] == 1) {
                $filename = 'report_abandoned_cart' . Hashids::encode($user->id) . '.' . $dataRequest['format'];

                (new AbandonedCartReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue('high');
            } else if ($dataRequest['recovery_type'] == 3) {
                $filename = 'report_card_refused' . Hashids::encode($user->id) . '.' . $dataRequest['format'];

                (new CardRefusedReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue('high');
            } else if ($dataRequest['recovery_type'] == 4) {
                $filename = 'report_pix_expired' . Hashids::encode($user->id) . '.' . $dataRequest['format'];

                (new PixExpiredReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue('high');
            } else {
                $filename = 'report_billet_expired' . Hashids::encode($user->id) . '.' . $dataRequest['format'];

                (new BilletExpiredReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue('high');
            }

            return response()->json(['message' => 'A exportação começou', 'email' => $dataRequest['email']]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar gerar o arquivo Excel.'], 400);
        }
    }

    public function getPixOverdue(Request $request)
    {
        $data                 = $request->all();
        $salesRecoveryService = new SalesRecoveryService();

        $projectId = null;
        if (!empty($data['project'])) {
            $projectId = current(Hashids::decode($data['project']));
        }

        $client = null;
        if (!empty($data['client'])) {
            $client = $data['client'];
        }

        $clientDocument = null;
        if (!empty($data['client_document'])) {
            $clientDocument = $data['client_document'];
        }

        $plan = null;
        if (!empty($data['plan'])) {
            $plan = $data['plan'];
        }

        $dateStart = null;
        $dateEnd   = null;

        $dateRange = FoxUtils::validateDateRange($data["date_range"]);
        if (!empty($data["date_type"]) && $dateRange) {
            $dateStart = $dateRange[0] . ' 00:00:00';
            $dateEnd   = $dateRange[1] . ' 23:59:59';
        }

        $paymentMethod = (new Sale())->present()->getPaymentType('pix');
        $status        = [5];

        $sales = $salesRecoveryService->getSaleExpiredOrRefused(
            $paymentMethod,
            $status,
            $projectId,
            $dateStart,
            $dateEnd,
            $client,
            $clientDocument,
            $plan
        );

        return SalesRecoveryCardRefusedResource::collection($sales);
    }
}
