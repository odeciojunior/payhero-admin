<?php

namespace Modules\Api\Http\Controllers\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Api\Http\Requests\V1\SalesApiRequest;
use Modules\Api\Transformers\V1\SalesApiResource;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Services\Api\SaleApiService;
use Modules\Core\Services\SaleService;
use Modules\Sales\Transformers\TransactionResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

class SalesApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = $request->all();
            $verifyRequest = new SalesApiRequest();
            $validator = Validator::make($data, $verifyRequest->getSalesRules(), $verifyRequest->messages());

            if ($validator->fails()) {
                return response()->json($validator->errors()->toArray());
            }

            $saleModel = new Sale();
            $transactionModel = new Transaction();
            $transactions = $transactionModel->join("sales", "sales.id", "transactions.sale_id");

            if (!empty($data["transaction"])) {
                $transaction_id = current(Hashids::connection("sale_id")->decode($data["transaction"]));

                $transactions->where("sales.id", $transaction_id);
            }

            if (!empty($data["company"])) {
                $companies = Hashids::decode($data["company"]);
            } else {
                $companies = Company::where("user_id", request()->user_id)
                    ->pluck("id")
                    ->toArray();
            }

            $transactions->whereIn("transactions.company_id", $companies);

            if (!empty($data["user"])) {
                $user_id = current(Hashids::decode($data["user"]));
                $subsellers = User::where("subseller_owner_id", request()->user_id)
                    ->where("id", $user_id)
                    ->pluck("id")
                    ->toArray();
            } else {
                $subsellers = User::where("subseller_owner_id", request()->user_id)
                    ->pluck("id")
                    ->toArray();
                array_push($subsellers, request()->user_id);
            }

            $transactions->whereIn("sales.owner_id", $subsellers);

            if (!empty($data["status"])) {
                $transactions->where("sales.status", $saleModel->present()->getStatus($data["status"]));
            } else {
                $transactions->whereIn("sales.status", [
                    Sale::STATUS_APPROVED,
                    Sale::STATUS_PENDING,
                    Sale::STATUS_CHARGEBACK,
                    Sale::STATUS_REFUNDED,
                    Sale::STATUS_PARTIAL_REFUNDED,
                    Sale::STATUS_IN_REVIEW,
                    Sale::STATUS_CANCELED_ANTIFRAUD,
                    Sale::STATUS_IN_DISPUTE,
                ]);
            }

            if (!empty($data["date_type"]) && !empty($data["date_range"])) {
                $dateType = $data["date_type"];
                $dateRange = foxutils()->validateDateRange($data["date_range"]);

                $transactions->whereBetween("sales." . $dateType, [
                    $dateRange[0] . " 00:00:00",
                    $dateRange[1] . " 23:59:59",
                ]);
            }

            $query = $transactions->orderByDesc("sales.start_date")->simplePaginate(10);

            return SalesApiResource::collection($query);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Erro ao carregar vendas"], 400);
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
