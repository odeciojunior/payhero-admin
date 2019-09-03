<?php

namespace Modules\SalesRecovery\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Entities\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Project;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\UserProject;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Services\SalesRecoveryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\SalesRecovery\Transformers\SalesRecoveryIndexResourceTransformer;
use Modules\SalesRecovery\Transformers\SalesRecoverydetailsResourceTransformer;
use Modules\SalesRecovery\Transformers\SalesRecoveryCartAbandonedDetailsResourceTransformer;

class SalesRecoveryApiController extends Controller
{
    /**
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $userProjectModel = new UserProject();
            $projectModel     = new Project();

            $userProjects = $userProjectModel->where('user_id', auth()->user()->id)->pluck('project_id');

            $projects = $projectModel->whereIn('id', $userProjects)->get();

            return SalesRecoveryIndexResourceTransformer::collection($projects);
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

                return $salesRecoveryService->verifyType($request->input('type'), $projectId, $request->input('start_date'), $endDate, $client);
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
}
