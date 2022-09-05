<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Api\Transformers\V1\SalesApiResource;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\Api\SaleApiService;
use Modules\Sales\Transformers\TransactionResource;
use Vinkla\Hashids\Facades\Hashids;

class SalesApiController extends Controller
{
    public function index()
    {
        try {
            $page = request()->input("page");
            if (!empty($page) && (!is_numeric($page) || $page < 0)) {
                return response()->json(["message" => "Erro ao listar venda(s)"], 400);
            }

            $subsellers = User::where("subseller_owner_id", request()->user_id)->pluck("id")->toArray();

            array_push($subsellers, request()->user_id);

            $saleModel = new Sale();
            $sales = $saleModel
                ->whereIn("owner_id", $subsellers)
                ->whereIn("status", [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_PENDING,
                    Sale::STATUS_CHARGEBACK,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_PARTIAL_REFUNDED,
                    Sale::STATUS_IN_REVIEW,
                    Sale::STATUS_CANCELED_ANTIFRAUD,
                    Sale::STATUS_IN_DISPUTE,
                ])
                ->orderBy("id", "desc")
                ->simplePaginate(10);

            return SalesApiResource::collection($sales);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao carregar vendas"], 400);
        }
    }

    public function showSales($id)
    {
        try {
            $idDecode = current(Hashids::connection('sale_id')->decode($id));

            $subsellers = User::where("subseller_owner_id", request()->user_id)->pluck("id")->toArray();
            array_push($subsellers, request()->user_id);

            $saleModel = new Sale();
            $sales = $saleModel
                ->whereIn("owner_id", $subsellers)
                ->whereIn("status", [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_PENDING,
                    Sale::STATUS_CHARGEBACK,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_PARTIAL_REFUNDED,
                    Sale::STATUS_IN_REVIEW,
                    Sale::STATUS_CANCELED_ANTIFRAUD,
                    Sale::STATUS_IN_DISPUTE,
                ])
                ->where("id", $idDecode)
                ->first();

            return new SalesApiResource($sales);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao carregar venda"], 400);
        }
    }
}
