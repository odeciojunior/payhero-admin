<?php

namespace Modules\Finances\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Finances\Exports\Reports\ExtractReportExport;
use Modules\Finances\Exports\Reports\ExtractReportExportGateway;
use Spatie\Activitylog\Models\Activity;
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
        try {
            if (empty($request->input('company'))) {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
            }

            $company = Company::find(hashids_decode($request->input('company')));
            $gateway = Gateway::find(hashids_decode($request->input('gateway_id')));

            if (empty($company) || empty($gateway)) {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
            }

            if (Gate::denies('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão'], Response::HTTP_FORBIDDEN);
            }

            $companyService = new CompanyBalanceService($company, $gateway->getService());

            $blockedBalance = $companyService->getBalance(CompanyBalanceService::BLOCKED_BALANCE);
            $blockedBalancePending = $companyService->getBalance(CompanyBalanceService::BLOCKED_PENDING_BALANCE);
            $pendingBalance = $companyService->getBalance(CompanyBalanceService::PENDING_BALANCE);
            $availableBalance = $companyService->getBalance(CompanyBalanceService::AVAILABLE_BALANCE);

            $blockedBalanceTotal = $blockedBalancePending + $blockedBalance;
            $totalBalance = $availableBalance + $pendingBalance + $blockedBalanceTotal;
            $pendingDebtBalance = $companyService->getBalance(CompanyBalanceService::PENDING_DEBT_BALANCE);

            return response()->json(
                [
                    'available_balance' => foxutils()->formatMoney($availableBalance / 100),
                    'total_balance' => foxutils()->formatMoney($totalBalance / 100),
                    'pending_balance' => foxutils()->formatMoney($pendingBalance / 100),
                    'blocked_balance' => foxutils()->formatMoney($blockedBalanceTotal / 100),
                    'pending_debt_balance' => foxutils()->formatMoney($pendingDebtBalance / 100)
                ]
            );
        } catch (Exception $e) {
            report($e);
            return response()->json(['message' => 'Ocorreu algum erro, tente novamente!',], 400);
        }
    }

    public function export(Request $request): JsonResponse
    {
        try {
            $dataRequest = $request->all();

            $user = auth()->user();
            $filename = 'extract_report_' . Hashids::encode($user->id) . '.' . $dataRequest['format'];

            (new ExtractReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue('high');

            $email = !empty($dataRequest['email']) ? $dataRequest['email'] : $user->email;

            return response()->json(['message' => 'A exportação começou', 'email' => $email]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar gerar o relatório.'], 400);
        }
    }

    public function getStatementResume(Request $request)
    {
        $company = Company::find(hashids_decode($request->company_id));
        $companyService = new CompanyBalanceService($company);
        return response()->json($companyService->getResumes());
    }

    public function getAcquirers($companyId=null)
    {
        $companies = null;
        if(empty($companyId)){
            $companies = Company::with('user')->where('user_id', auth()->user()->account_owner_id)->get();
        }else{
            $companies = Company::where('id',$companyId)->get();
        }
        $gatewayIds = [];

        foreach($companies as $company){
            $companyService = new CompanyBalanceService($company);
            $gatewayIds = array_merge($gatewayIds,$companyService->getAcquirers());
        }

        return response()->json(['data'=>array_unique($gatewayIds)]);
    }
}
