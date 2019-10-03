<?php

namespace Modules\Checkouts\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Checkouts\Transformers\CheckoutIndexResource;
use Modules\Checkouts\Transformers\CheckoutResource;
use Modules\Core\Entities\Checkout;
use Modules\Core\Services\CheckoutService;
use Modules\Core\Services\FoxUtils;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class CheckoutApiController
 * @package Modules\Checkouts\Http\Controllers
 */
class CheckoutApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $requestValidated = Validator::make($request->all(), [
                'project'    => 'required|string',
                'status'     => 'required|string',
                'start_date' => 'nullable',
                'end_date'   => 'nullable',
                'client'     => 'nullable|string',
            ]);
            if ($requestValidated->fails()) {
                return response()->json([
                                            'message' => 'Erro ao listar carrinho abandonado, tente novamente mais tarde',
                                        ], 400);
            } else {
                $checkoutService = new CheckoutService();

                $projectId = FoxUtils::decodeHash($request->input('project'));

                if (!empty($request->input('client'))) {
                    $clientId = $request->input('client');
                } else {
                    $clientId = null;
                }

                if (!empty($request->input('end_date'))) {
                    $endDate = date('Y-m-d', strtotime($request->input('end_date') . ' + 1 day'));
                } else {
                    $endDate = null;
                }

                if (!empty($request->input('start_date'))) {
                    $startDate = date('Y-m-d', strtotime($request->input('start_date')));
                } else {
                    $startDate = null;
                }

                $checkouts = $checkoutService->getAbandonedCart($projectId, $startDate, $endDate, $clientId);

                return CheckoutIndexResource::collection($checkouts);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados recuperação de vendas (CheckoutApiController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }

    public function show($id)
    {
        try {
            if (isset($id)) {

                $checkoutModel = new Checkout();

                $id = current(Hashids::decode($id));

                $checkout = $checkoutModel->find($id);

                return new CheckoutResource($checkout);
            } else {
                return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados recuperação de vendas (CheckoutApiController - index)');
            report($e);

            return response()->json(['message' => 'Ocorreu um erro, tente novamente mais tarde'], 400);
        }
    }
}
