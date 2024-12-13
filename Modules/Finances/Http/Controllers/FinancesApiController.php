<?php

namespace Modules\Finances\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Pixel;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Finances\Exports\Reports\ExtractReportExport;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class FinancesApiController
 * @package Modules\Finances\Http\Controllers
 */
class FinancesApiController extends Controller
{
    public function getBalances(Request $request): JsonResponse
    {
        $pixel = new Pixel();

        try {
            $company = Company::find(hashids_decode($request->input("company")));
            $gatewayId = foxutils()->isProduction() ? Gateway::VEGA_PRODUCTION_ID : Gateway::VEGA_SANDBOX_ID; //hashids_decode($request->input("gateway_id"));

            if (empty($company) || empty($gatewayId)) {
                return response()->json(["message" => "Ocorreu algum erro, tente novamente!"], 400);
            }

            if ($company->id != Company::DEMO_ID && Gate::denies("edit", [$company])) {
                return response()->json(["message" => __('messages.unauthorized')], Response::HTTP_FORBIDDEN);
            }

            $gatewayService = Gateway::getServiceById($gatewayId);
            $gatewayService->setCompany($company);

            $balanceResume = $gatewayService->getResume();

            return response()->json([
                "available_balance" => foxutils()->formatMoney(($balanceResume["available_balance"] ?? 0) / 100),
                "total_balance" => foxutils()->formatMoney(($balanceResume["total_balance"] ?? 0) / 100),
                "pending_balance" => foxutils()->formatMoney(($balanceResume["pending_balance"] ?? 0) / 100),
                "security_reserve_balance" => foxutils()->formatMoney(
                    ($balanceResume["security_reserve_balance"] ?? 0) / 100
                ),
                "blocked_balance" => foxutils()->formatMoney(($balanceResume["blocked_balance"] ?? 0) / 100),
                "pending_debt_balance" => foxutils()->formatMoney(($balanceResume["pending_debt_balance"] ?? 0) / 100),
            ]);
        } catch (Exception $e) {
            report($e);
            return response()->json(["message" => "Ocorreu algum erro, tente novamente!"], 400);
        }
    }

    public function export(Request $request): JsonResponse
    {
        try {
            $dataRequest = $request->all();

            $user = auth()->user();
            $filename = "extract_report_" . Hashids::encode($user->id) . "." . $dataRequest["format"];

            (new ExtractReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue("high");

            $email = !empty($dataRequest["email"]) ? $dataRequest["email"] : $user->email;

            return response()->json(["message" => "A exportação começou", "email" => $email]);
        } catch (Exception $e) {
            report($e);

            return response()->json(["message" => "Erro ao tentar gerar o relatório."], 400);
        }
    }

    public function getStatementResume(Request $request)
    {
        $company = Company::find(hashids_decode($request->company_id));
        $companyService = new CompanyBalanceService($company);
        $statementResumes = $companyService->getResumes();
        foreach ($statementResumes as &$statementResume) {
            if (!is_array($statementResume) || empty($statementResume)) {
                continue;
            }
            foreach ($statementResume as &$data) {
                $data = is_int($data) ? foxutils()->formatMoney($data / 100) : $data;
            }
        }
        return response()->json($statementResumes);
    }

    public function getAcquirers($companyId = null)
    {
        $companies = null;
        if (empty($companyId)) {
            $companies = Company::with("user")
                ->where("user_id", auth()->user()->account_owner_id)
                ->get();
        } else {
            $companies = Company::where("id", hashids_decode($companyId))->get();
        }
        $gatewayIds = [];

        foreach ($companies as $company) {
            $companyService = new CompanyBalanceService($company);
            $gatewayIds = array_merge($gatewayIds, $companyService->getAcquirers());
        }

        return response()->json(["data" => array_unique($gatewayIds)]);
    }
}
