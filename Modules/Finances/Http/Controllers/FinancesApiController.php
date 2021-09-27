<?php

namespace Modules\Finances\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\RemessaOnlineService;
use Modules\Finances\Exports\Reports\ExtractReportExport;
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

            $company = Company::find(current(Hashids::decode($request->input('company'))));

            if (empty($company)) {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
            }

            if (Gate::denies('edit', [$company])) {
                return response()->json(['message' => 'Sem permissão'], Response::HTTP_FORBIDDEN);
            }

            $gateway = Gateway::find(hashids_decode($request->input('gateway_id')));

            $companyService = new CompanyBalanceService($company, $gateway->getService());
            // $companyService = new CompanyBalanceService($company);

            $blockedBalance = $companyService->getBalance(CompanyBalanceService::BLOCKED_BALANCE);
            $blockedBalancePending = $companyService->getBalance(CompanyBalanceService::BLOCKED_PENDING_BALANCE);
            $pendingBalance = $companyService->getBalance(CompanyBalanceService::PENDING_BALANCE) - $blockedBalancePending;
            $availableBalance = $companyService->getBalance(CompanyBalanceService::AVAILABLE_BALANCE);
            $totalBalance = $availableBalance + $pendingBalance;
            $availableBalance -= $blockedBalance;
            $blockedBalanceTotal = $blockedBalancePending + $blockedBalance;

            return response()->json(
                [
                    'available_balance' => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                    'total_balance' => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                    'pending_balance' => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                    'blocked_balance' => number_format(intval($blockedBalanceTotal) / 100, 2, ',', '.'),
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

            activity()->tap(
                function (Activity $activity) {
                    $activity->log_name = 'visualization';
                }
            )->log('Exportou tabela ' . $dataRequest['format'] . ' de transferências');

            $user = auth()->user();

            $filename = 'extract_report_' . Hashids::encode($user->id) . '.' . $dataRequest['format'];

            (new ExtractReportExport($dataRequest, $user, $filename))->queue($filename)->allOnQueue('high');

            $email = !empty($dataRequest['email']) ? $dataRequest['email'] : $user->email;

            return response()->json(['message' => 'A exportação começou', 'email' => $email]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar gerar o arquivo Excel.'], 200);
        }
    }
}
