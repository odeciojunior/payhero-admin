<?php


namespace Modules\Finances\Http\Controllers;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBalanceLog;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\RemessaOnlineService;
use Modules\Finances\Exports\Reports\ExtractReportExport;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class OldFinancesApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
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

            $companyService = new CompanyBalanceService($company);

            $pendingBalance = $companyService->getBalance(CompanyBalanceService::PENDING_BALANCE);

            $availableBalance = $companyService->getBalance(CompanyBalanceService::AVAILABLE_BALANCE);

            $totalBalance = $availableBalance + $pendingBalance;

            $blockedBalance = $companyService->getBalance(CompanyBalanceService::BLOCKED_BALANCE);
            $blockedBalancePending = $companyService->getBalance(CompanyBalanceService::BLOCKED_PENDING_BALANCE);
            $blockedBalanceTotal = $blockedBalancePending + $blockedBalance;

            $pendingDebtBalance = $companyService->getBalance(CompanyBalanceService::PENDING_DEBT_BALANCE);

            return response()->json(
                [
                    'available_balance' => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                    'total_balance' => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                    'pending_balance' => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                    'blocked_balance' => number_format(intval($blockedBalanceTotal) / 100, 2, ',', '.'),
                    'pending_debt_balance' => number_format(intval($pendingDebtBalance) / 100, 2, ',', '.'),
                ]
            );
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente!',], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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

            return response()->json(['message' => 'A exportação começou', 'email' => $user->email]);
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar gerar o arquivo Excel.'], 200);
        }
    }
}
