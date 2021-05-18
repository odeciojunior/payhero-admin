<?php

namespace Modules\SalesRecovery\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Checkout;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Contracts\View\Factory;
use Modules\Core\Services\SalesRecoveryService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class SalesRecoveryController
 * @package Modules\SalesRecovery\Http\Controllers
 */
class SalesRecoveryController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('salesrecovery::index');
    }
    
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getDetails(Request $request)
    {
        try {
            $checkoutModel        = new Checkout();
            $salesRecoveryService = new SalesRecoveryService();
            $details              = null;

            if ($request->has('checkout') && !empty($request->input('checkout'))) {
                $checkoutId = current(Hashids::decode($request->input('checkout')));
                $checkout   = $checkoutModel->find($checkoutId);
                if (!empty($checkout)) {
                    $details = $salesRecoveryService->getSalesCheckoutDetails($checkout);
                } else {
                    $details = $salesRecoveryService->getSalesCartOrBoletoDetails($checkoutId);
                }

                if ($details == null) {
                    return response()->json(['message' => 'Ocorreu algum erro']);
                } else {
                    return response()->json($details->render());
                }
            } else {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde']);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar detalhes do carrinho abandonado');
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde']);
        }
    }
}


