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
     * List cart abandoned
     */
    public function index(Request $request)
    {
        try {
            $requestValidate = $request->validate([
                'project'         => 'nullable|string',
                'status'          => 'required',
                'date_range'      => 'required',
                'client'          => 'nullable|string',
                'client_document' => 'nullable|string',
                'plan'            => 'nullable|string',
            ]);

            $checkoutService = new CheckoutService();

            $projectId = FoxUtils::decodeHash($requestValidate['project']);

            if (!empty($requestValidate['client'])) {
                $clientId = $requestValidate['client'];
            } else {
                $clientId = null;
            }

            $clientDocument = null;
            if (!empty($requestValidate['client_document'])) {
                $clientDocument = $requestValidate['client_document'];
            }

            $plan = null;
            if (!empty($requestValidate['plan'])) {
                $plan = $requestValidate['plan'];
            }

            $dateRange = FoxUtils::validateDateRange($requestValidate['date_range']);
            $startDate = null;
            $endDate   = null;
            if (!empty($dateRange) && $dateRange) {
                $startDate = $dateRange[0] . ' 00:00:00';
                $endDate   = $dateRange[1] . ' 23:59:59';
            }

            $checkouts = $checkoutService->getAbandonedCart($projectId, $startDate, $endDate, $clientId, $clientDocument, $plan);

            return CheckoutIndexResource::collection($checkouts);
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
