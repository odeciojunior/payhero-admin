<?php

namespace Modules\Finances\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\AnticipationService;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\RemessaOnlineService;
use Modules\Core\Services\SaleService;
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
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalances(Request $request)
    {
        try {
            $companyModel = new Company();

            $transactionModel     = new Transaction();
            $companyService       = new CompanyService();
            $remessaOnlineService = new RemessaOnlineService();
            $anticipationService  = new AnticipationService();
            $saleService          = new SaleService();

            $pendingBalance = 0;

            if ($request->has('company') && !empty($request->input('company'))) {
                $companyId = current(Hashids::decode($request->input('company')));

                $company = $companyModel->find($companyId);

                if (Gate::denies('edit', [$company])) {
                    return response()->json([
                                                'message' => 'Sem permissão',
                                            ], Response::HTTP_FORBIDDEN
                    );
                }

                if (!empty($company)) {

                    $pendingTransactions = $transactionModel->where('company_id', $company->id)
                                                            ->where('status_enum', $transactionModel->present()
                                                                                                    ->getStatusEnum('paid'))
                                                            ->whereDate('release_date', '>', now()->startOfDay())
                                                            ->select(DB::raw('sum( value ) as pending_balance'))
                                                            ->first();

                    $transactionsAnticipated = $transactionModel->with('anticipatedTransactions')
                                                                ->where('company_id', $company->id)
                                                                ->where('status_enum', $transactionModel->present()
                                                                                                        ->getStatusEnum('anticipated'))
                                                                ->get();

                    $blockedBalance = $saleService->getBlockedBalance($companyId, auth()->user()->account_owner_id);

                    $pendingBalance += $pendingTransactions->pending_balance;

                    if (count($transactionsAnticipated) > 0) {
                        foreach ($transactionsAnticipated as $transactionAnticipated) {
                            $pendingBalance += $transactionAnticipated->value - $transactionAnticipated->anticipatedTransactions()
                                                                                                       ->first()->value;
                        }
                    }

                    $availableBalance = $company->balance;
                    $totalBalance     = $availableBalance + $pendingBalance;

                    if ($availableBalance < 1) {
                        $availableBalance += $blockedBalance;
                    } else {
                        $availableBalance -= $blockedBalance;
                    }

                    $currency          = $companyService->getCurrency($company);
                    $currencyQuotation = '';

                    if ($company->country != 'brazil') {
                        $currencyQuotation = $remessaOnlineService->getCurrentQuotation($currency);
                        $currencyQuotation = number_format((float) $currencyQuotation, 2, ',', '');
                    }

                    $antecipableBalance = $company->user->antecipation_enabled_flag ? $anticipationService->getAntecipableValue($company) : '000';

                    return response()->json(
                        [
                            'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                            'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                            'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                            'anticipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                            'currency'            => $currency,
                            'currencyQuotation'   => $currencyQuotation,
                            'blocked_balance'     => number_format(intval($blockedBalance) / 100, 2, ',', '.'),
                        ]
                    );
                } else {
                    return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
                }
            } else {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente!',], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function export(Request $request)
    {
        try {
            $dataRequest = $request->all();

            activity()->tap(function(Activity $activity) {
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
