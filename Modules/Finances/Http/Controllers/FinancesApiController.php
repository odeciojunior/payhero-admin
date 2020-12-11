<?php


namespace Modules\Finances\Http\Controllers;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\RemessaOnlineService;
use Modules\Finances\Exports\Reports\ExtractReportExport;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class FinancesApiController
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

            $companyId = current(Hashids::decode($request->input('company')));

            $company = Company::find($companyId);

            if (Gate::denies('edit', [$company])) {
                return response()->json(
                    [
                        'message' => 'Sem permissão',
                    ],
                    Response::HTTP_FORBIDDEN
                );
            }

            $companyService = new CompanyService();

            $pendingBalance = 0;

            if (empty($company)) {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
            }

            $pendingBalance = $companyService->getPendingBalance($company, CompanyService::STATEMENT_AUTOMATIC_LIQUIDATION_TYPE);

            $availableBalance = $company->balance;
            $totalBalance = $availableBalance + $pendingBalance;

            return response()->json(
                [
                    'available_balance' => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                    'total_balance' => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                    'pending_balance' => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                    'currency' => 'R$',
                    'currencyQuotation' => '0,00',
                    'blocked_balance' => '0,00',
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