<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Api\Http\Requests\V1\SalesApiRequest;
use Modules\Api\Transformers\V1\SalesApiResource;
use Modules\Core\Services\Api\V1\SalesApiService;

class SalesApiController extends Controller
{
    public function getSales(Request $request)
    {
        try {
            $data = $request->all();

            $verifyRequest = new SalesApiRequest();
            $validator = Validator::make(
                $data,
                $verifyRequest->getSalesRules(),
                $verifyRequest->messages()
            );

            if ($validator->fails()) {
                return response()->json($validator->errors()->toArray());
            }

            $sales = SalesApiService::getSalesQueryBuilder($data);

            return SalesApiResource::collection($sales->simplePaginate(10));
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao carregar vendas"], 400);
        }
    }
}
