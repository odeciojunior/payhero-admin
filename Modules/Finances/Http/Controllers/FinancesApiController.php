<?php

namespace Modules\Finances\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\AnticipatedTransaction;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\AnticipationService;
use Spatie\Activitylog\Models\Activity;
use Modules\Core\Services\CompanyService;
use Symfony\Component\HttpFoundation\Response;
use Modules\Core\Services\RemessaOnlineService;
use Modules\Finances\Exports\Reports\ExtractReportExport;

/**
 * Class FinancesApiController
 * @package Modules\Finances\Http\Controllers
 */
class FinancesApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalances(Request $request)
    {
        try {
            $companyModel = new Company();

            $transactionModel             = new Transaction();
            $companyService               = new CompanyService();
            $remessaOnlineService         = new RemessaOnlineService();
            $anticipationService          = new AnticipationService();

            $antecipableBalance = 0;
            $pendingBalance     = 0;

            if ($request->has('company') && !empty($request->input('company'))) {
                $companyId = current(Hashids::decode($request->input('company')));

                $company = $companyModel->find($companyId);

                if (Gate::denies('edit', [$company])) {
                    return response()->json([
                            'message' => 'Sem permissão',
                        ],Response::HTTP_FORBIDDEN
                    );
                }

                if (!empty($company)) {

                    $pendingBalance = $companyService->getPendingBalance($company);

                    $availableBalance   = $company->balance;
                    $totalBalance       = $availableBalance + $pendingBalance;

                    $currency          = $companyService->getCurrency($company);

                    $currencyQuotation = '';

                    if($company->country != 'brazil'){
                        $currencyQuotation = $remessaOnlineService->getCurrentQuotation($currency);
                        $currencyQuotation = number_format((float)$currencyQuotation, 2, ',', '');
                    }

                    $antecipableBalance = $company->user->antecipation_enabled_flag ? $anticipationService->getAntecipableValue($company) : '000';

                    return response()->json(
                        [
                            'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                            'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                            'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                            'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                            'anticipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                            'currency'            => $currency,
                            'currencyQuotation'   => $currencyQuotation,
                        ]
                    );
                } else {
                    return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
                }
            } else {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar da empresa (FinancesController - getBalances)');
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu algum erro, tente novamente!',
                ], 400);
        }
    }

    public function export(Request $request)
    {
        try {
            $dataRequest = $request->all();

            activity()->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Exportou tabela ' . $dataRequest['format'] . ' de transferências');

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
