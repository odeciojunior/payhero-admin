<?php

namespace Modules\Mobile\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Mobile\Transformers\SalesResource;
use Modules\Mobile\Transformers\StatementsResource;

class MobileController extends Controller
{
    /**
     * Returns the sales of the user's company.
     *
     * @return JsonResponse
     */
    public function sales(Request $request)
    {
        try {
            $request->validate([
                "company_id" => "required|string",
            ]);

            $companyId = hashids_decode($request->company_id);

            $relations = [
                "sale",
                "sale.project",
                "sale.plansSales",
                "sale.productsPlansSale.plan",
                "sale.productsPlansSale.product",
            ];

            $sales = Transaction::with($relations)
                ->selectRaw("transactions.*")
                ->join("sales", "sales.id", "transactions.sale_id")
                ->whereNull("invitation_id")
                ->where("company_id", $companyId)
                ->orderByDesc("sales.start_date");

            if (!$request->status) {
                $status = [1, 2, 4, 7, 8, 12, 20, 21, 22, 24];
            } else {
                $status = explode(",", $request->status);
                $status = in_array(7, $status)
                    ? array_merge($status, [22])
                    : $status;
            }

            if (!empty($status)) {
                $sales->whereHas("sale", function ($query) use ($status) {
                    $query->whereIn("status", $status);
                });
            }

            if ($request->has("limit")) {
                $sales->limit($request->limit);
            }

            return SalesResource::collection($sales->get());
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Erro ao carregar vendas"],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Returns the financial values ​​of each gateway.
     *
     * @return JsonResponse
     */
    public function statementsResume(Request $request)
    {
        try {
            $request->validate([
                "company_id" => "required|string",
            ]);

            $companyId = hashids_decode($request->company_id);
            $company = Company::find($companyId);

            if (!$company) {
                return response()->json(
                    ["message" => "Empresa não encontrada"],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $companyService = new CompanyBalanceService($company);
            $statementResumes = $companyService->getResumeTotals($request);

            return StatementsResource::collection($statementResumes);
        } catch (Exception $e) {
            report($e);
            return response()->json(
                ["message" => "Erro ao carregar dados"],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
