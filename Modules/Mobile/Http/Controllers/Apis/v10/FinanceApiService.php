<?php


namespace Modules\Mobile\Http\Controllers\Apis\v10;


use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Transfers\Transformers\TransfersResource;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class FinanceApiService
 * @package Modules\Mobile\Http\Controllers\Apis\v10
 */
class FinanceApiService {

    /**
     * FinanceApiService constructor.
     */
    public function __construct() {  }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function financeGetData(Request $request) {

        try {
            $balances = $this->getBalances($request);
            $transactions = $this->getTransactions($request);

            return response()->json(compact('balances', 'transactions'), 200);

        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da dashboard (FinanceApiService - financeGetData)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, tente novamente mais tarde',
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getTransactions(Request $request) {

        try {
            $transfersModel = new Transfer();

            $transfers = $transfersModel
                ->select('transfers.*', 'transaction.sale_id', 'transaction.company_id', 'transaction.currency', 'transaction.status',
                    'transaction.antecipable_value', 'anticipatedtransaction.anticipation_id', 'anticipatedtransaction.created_at as anticipationCreatedAt')
                ->leftJoin('transactions as transaction', 'transaction.id', 'transfers.transaction_id')
                ->leftJoin('anticipated_transactions as anticipatedtransaction', 'anticipatedtransaction.transaction_id', 'transfers.transaction_id')
                ->where('transfers.company_id', current(Hashids::decode($request->company)))
                ->orWhere('transaction.company_id', current(Hashids::decode($request->company)))
                ->orderBy('id', 'DESC');

            return TransfersResource::collection($transfers->paginate(10));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar lista de transferencias (FinanceApiService - getTransactions)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, tente novamente mais tarde!',
            ], 400);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalances(Request $request)
    {
        try {
            /** @var Company $companyModel */
            $companyModel = new Company();
            /** @var Transaction $transactionModel */
            $transactionModel   = new Transaction();
            $antecipableBalance = 0;
            $pendingBalance     = 0;
            if ($request->has('company') && !empty($request->input('company'))) {
                $companyId = current(Hashids::decode($request->input('company')));
                /** @var Company $company */
                $company = $companyModel->newQuery()->find($companyId);
                if (!empty($company)) {
                    //                    $pendingTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                    //                                                            ->where('status', 'paid')
                    //                                                            ->whereDate('release_date', '>', now()->startOfDay())
                    //                                                            ->get();
                    $pendingTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                        ->where('status', 'paid')
                        ->whereDate('release_date', '>', now()->startOfDay())
                        ->select(DB::raw('sum( value ) as pending_balance'))
                        ->first();
                    $pendingBalance      += $pendingTransactions->pending_balance;
                    $anticipableTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                        ->where('status', 'anticipated')
                        ->whereDate('release_date', '>', now()->startOfDay())
                        ->select(DB::raw('sum( value - antecipable_value ) as pending_balance'))
                        ->first();
                    $pendingBalance += $anticipableTransactions->pending_balance;
                    $antecipableTransactions = $transactionModel->newQuery()->where('company_id', $company->id)
                        ->where('status', 'paid')
                        ->whereDate('release_date', '>', Carbon::today())
                        ->whereDate('antecipation_date', '<=', Carbon::today())
                        ->select(DB::raw('sum( antecipable_value ) as antecipable_balance'))
                        ->first();

                    $antecipableBalance += $antecipableTransactions->antecipable_balance;
                    $availableBalance = $company->balance;
                    $totalBalance     = $availableBalance + $pendingBalance;

                    return [
                            'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
                            'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
                            'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
                            'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
                            'currency'            => $company->country == 'usa' ? '$' : 'R$',
                        ];
                } else {
                    return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
                }
            } else {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente!'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar da empresa (FinanceApiController - getBalances)');
            report($e);

            return response()->json(
                [
                    'message' => 'Ocorreu algum erro, tente novamente!',
                ], 400);
        }
    }

}
