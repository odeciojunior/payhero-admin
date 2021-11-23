<?php

namespace Modules\Checkouts\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Checkouts\Transformers\CheckoutIndexResource;
use Modules\Checkouts\Transformers\CheckoutResource;
use Modules\Core\Entities\Checkout;
use Modules\Core\Services\CheckoutService;
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
            $request->validate(
                [
                    'project' => 'nullable|string',
                    'recovery_type' => 'required',
                    'date_range' => 'required',
                    'client' => 'nullable|string',
                    'client_document' => 'nullable|string',
                    'plan' => 'nullable|string',
                ]
            );

            $checkouts = (new CheckoutService())->getAbandonedCart();
            return CheckoutIndexResource::collection($checkouts);
            
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu um erro, tente novamente mais tarde',
                ],
                400
            );
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
