<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Api\Http\Requests\V1\SalesApiRequest;
use Modules\Api\Transformers\V1\SalesApiResource;
use Modules\Core\Services\Api\V1\SalesApiService;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\SaleService;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class SalesApiController extends Controller
{
    public function getSales(Request $request)
    {
        try {
            $data = $request->all();

            $verifyRequest = new SalesApiRequest();
            $validator = Validator::make($data, $verifyRequest->getSalesRules(), $verifyRequest->messages());

            if ($validator->fails()) {
                return response()->json($validator->errors()->toArray());
            }
            $sales = SalesApiService::getSalesQueryBuilder($data);

            return SalesApiResource::collection($sales->simplePaginate(10));
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao listar vendas"], 400);
        }
    }

    public function refundSales(Request $request)
    {
        try {
            $saleId = current(Hashids::connection("sale_id")->decode($request->id));

            $saleModel = new Sale();
            $sale = $saleModel->where(["id" => $saleId, "owner_id" => $request->user_id])->first();

            if (empty($sale)) {
                return response()->json(["message" => "Venda não encontrada"], 400);
            }

            activity()
                ->on($saleModel)
                ->tap(function (Activity $activity) use ($saleId) {
                    $activity->log_name = "estorno";
                    $activity->subject_id = $saleId;
                })
                ->log("Tentativa de estorno via API da transação: #" . $request->id);

            $saleService = new SaleService();
            $data = $saleService->refund($sale, $request->refund_observation);
            $status = $data["status"] == "success" ? 200 : 400;

            return response()->json(["message" => $data["message"]], $status);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao tentar estornar venda"], 400);
        }
    }
}
