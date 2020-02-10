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
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\CompanyService;
use Symfony\Component\HttpFoundation\Response;
use Modules\Core\Services\RemessaOnlineService;

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

                    $pendingTransactions     = $transactionModel->newQuery()->where('company_id', $company->id)
                                                                ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
                                                                ->whereDate('release_date', '>', now()->startOfDay())
                                                                ->select(DB::raw('sum( value ) as pending_balance'))
                                                                ->first();
                    $pendingBalance          += $pendingTransactions->pending_balance;
                    $antecipableTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                                                                ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
                                                                ->whereDate('release_date', '>', Carbon::today())
                                                                ->whereDate('antecipation_date', '<=', Carbon::today())
                                                                ->select(DB::raw('sum( antecipable_value ) as antecipable_balance'))
                                                                ->first();

                    $antecipableBalance += $antecipableTransactions->antecipable_balance;
                    $availableBalance   = $company->balance;
                    $totalBalance       = $availableBalance + $pendingBalance;

                    $currency          = $companyService->getCurrency($company);

                    $currencyQuotation = '';

                    if($company->country != 'brazil'){
                        $currencyQuotation = $remessaOnlineService->getCurrentQuotation($currency);
                        $currencyQuotation = number_format((float)$currencyQuotation, 2, ',', '');
                    }

                    return response()->json(
                        [
                            'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                            'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                            'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                            'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
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
}
