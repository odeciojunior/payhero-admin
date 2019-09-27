<?php

namespace Modules\Deliveries\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Delivery;
use Modules\Deliveries\Transformers\DeliveryResource;

/**
 * Class DeliveryApiController
 * @package Modules\Deliveries\Http\Controllers
 */
class DeliveryApiController extends Controller
{
    /**
     * @param $saleId
     * @param $deliveryId
     * @return JsonResponse|DeliveryResource
     */
    public function show($saleId, $deliveryId)
    {
        try {
            if (!empty($saleId) || !empty($deliveryId)) {
                $deliveryModel = new Delivery();

                $delivery = $deliveryModel->find($deliveryId);

                if (!empty($delivery)) {
                    return new DeliveryResource($delivery);
                } else {
                    return response()->json([
                                                'message' => 'Ocorreu um erro,dados invalidos',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro,dados invalidos',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados delivery (DeliveryApiController - show)');
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                    ], 400);
        }
    }
}
