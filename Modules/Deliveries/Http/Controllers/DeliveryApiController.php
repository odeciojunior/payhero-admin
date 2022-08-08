<?php

namespace Modules\Deliveries\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Delivery;
use Modules\Deliveries\Transformers\DeliveryResource;

class DeliveryApiController extends Controller
{
    public function show($deliveryId)
    {
        try {
            if (empty($deliveryId)) {
                return response()->json(
                    [
                        "message" => "Ocorreu um erro,dados invalidos",
                    ],
                    400
                );
            }

            $delivery = Delivery::find(hashids_decode($deliveryId));

            if (!empty($delivery)) {
                return new DeliveryResource($delivery);
            }

            return response()->json(
                [
                    "message" => "Ocorreu um erro,dados invalidos",
                ],
                400
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    "message" => "Ocorreu um erro, tente novamente mais tarde",
                ],
                400
            );
        }
    }
}
