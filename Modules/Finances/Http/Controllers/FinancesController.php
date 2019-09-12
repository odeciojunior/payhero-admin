<?php

namespace Modules\Finances\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PagarMe\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Transaction;

/**
 * Class FinancesController
 * @package Modules\Finances\Http\Controllers
 */
class FinancesController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        $companyModel = new Company();

        $userCompanies = $companyModel->where('user_id', auth()->user()->id)->get()->toArray();

        return view('finances::index', [
            'companies' => $userCompanies,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalances(Request $request)
    {
        try {
            $companyModel     = new Company();
            $transactionModel = new Transaction();

            $antecipableBalance = 0;
            $pendingBalance     = 0;
            if ($request->has('company') && !empty($request->input('company'))) {
                $companyId = current(Hashids::decode($request->input('company')));

                $company = $companyModel->find($companyId);

                if (!empty($company)) {
                    $pendingTransactions = $transactionModel->where('company_id', $company->id)
                                                            ->where('status', 'paid')
                                                            ->whereDate('release_date', '>', Carbon::today()
                                                                                                   ->toDateString())
                                                            ->get();

                    if (count($pendingTransactions)) {
                        foreach ($pendingTransactions as $pendingTransaction) {
                            $pendingBalance += $pendingTransaction->value;
                        }
                    }

                    $anticipableTransactions = $transactionModel->where('company_id', $company->id)
                                                                ->where('status', 'anticipated')
                                                                ->whereDate('release_date', '>', Carbon::today()
                                                                                                       ->toDateString())
                                                                ->get();

                    if (count($anticipableTransactions)) {
                        foreach ($anticipableTransactions as $anticipableTransaction) {
                            $pendingBalance += $anticipableTransaction->value - $anticipableTransaction->antecipable_value;
                        }
                    }

                    $antecipableTransactions = $transactionModel->where('company_id', $company->id)
                                                                ->where('status', 'paid')
                                                                ->whereDate('release_date', '>', Carbon::today())
                                                                ->whereDate('antecipation_date', '<=', Carbon::today())
                                                                ->get();

                    if (count($antecipableTransactions)) {
                        foreach ($antecipableTransactions as $antecipableTransaction) {
                            $antecipableBalance += $antecipableTransaction->antecipable_value;
                        }
                    }

                    $availableBalance = $company->balance;
                    $totalBalance     = $availableBalance + $pendingBalance;

                    return response()->json([
                                                'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                                                'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                                                'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                                                'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                                                'currency'            => $company->country == 'usa' ? '$' : 'R$',
                                            ]);
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


