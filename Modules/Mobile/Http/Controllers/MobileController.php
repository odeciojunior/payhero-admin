<?php

namespace Modules\Mobile\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\CompanyBalanceService;

class MobileController extends Controller
{
    /**
     * Returns user data.
     *
     * @return JsonResponse
     */
    public function user()
    {
        try {
            $user = User::select('id', 'name', 'photo', 'level', 'document', 'email', 'cellphone')->where('id', auth()->user()->id)->first();

            if($user) {
                return response()->json($user, 200);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dados não encontrados'
                ], 400);
            }
        }
        catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar dados'
            ], 400);
        }
    }

    /**
     * Returns the user's companies.
     *
     * @return JsonResponse
     */
    public function companies()
    {
        try {
            $companies = Company::where('user_id', auth()->user()->id)
                ->where('active_flag', true)
                ->orderBy('order_priority')
                ->get();

            if($companies) {
                return response()->json($companies, 200);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dados não encontrados'
                ], 400);
            }
        }
        catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar dados'
            ], 400);
        }
    }

    /**
     * Returns the company's financial data.
     *
     * @return JsonResponse
     */
    public function balances(Request $request)
    {
        try {
            $companyId = hashids_decode($request->company);
            $company = Company::find($companyId);

            if(empty($company)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dados não encontrados'
                ], 400);
            }

            $todayBalance = Sale::join('transactions as t', 't.sale_id', '=', 'sales.id')
                ->where('t.company_id', $companyId)
                ->whereDate('sales.end_date', Carbon::today()->toDateString())
                ->whereIn('t.status_enum', [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED])
                ->sum('t.value');

            $companyService = new CompanyBalanceService($company);
            $balancesResume = $companyService->getResumes();

            $availableBalance = array_sum(array_column($balancesResume, 'available_balance'));
            $pendingBalance = array_sum(array_column($balancesResume, 'pending_balance'));
            $blockedBalance = array_sum(array_column($balancesResume, 'blocked_balance'));
            $totalBalance = array_sum(array_column($balancesResume, 'total_balance'));

            return response()->json([
                'available_balance'     => $availableBalance,
                'pending_balance'       => $pendingBalance,
                'blocked_balance_total' => $blockedBalance,
                'total_balance'         => $totalBalance,
                'today_balance'         => $todayBalance
            ], 200);
        }
        catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar dados'
            ], 400);
        }
    }

    /**
     * Returns the sales of the user's company.
     *
     * @return JsonResponse
     */
    public function sales(Request $request)
    {
        try {
            $companyId = hashids_decode($request->company);

            $transactions = DB::table('transactions AS t')
                ->select('t.id', 'pps.name', 't.value', 's.start_date')
                ->join('sales AS s', 's.id', 't.sale_id')
                ->join('products_plans_sales AS pps', 'pps.sale_id', 's.id')
                ->whereNull(['t.invitation_id', 't.deleted_at', 's.deleted_at'])
                ->where('t.type', '<>', 8)
                ->where('s.status', 1)
                ->where('t.company_id', $companyId)
                ->orderBy('s.start_date', 'DESC')
                ->get();

            return response()->json($transactions, 200);
        }
        catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar dados'
            ], 400);
        }
    }

    /**
     * Returns the user's withdrawals.
     *
     * @return JsonResponse
     */
    public function withdrawals(Request $request)
    {
        try {
            $companyId = hashids_decode($request->company);
            $gatewayId = hashids_decode($request->gateway);

            $company = Company::find($companyId);
            $gateway = Gateway::find($gatewayId);

            if(empty($company) || empty($gateway)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Empresa não encontrada'
                ], 400);
            }

            $withdrawals = Withdrawal::select('id', 'value', 'status', 'created_at')
                ->where('company_id', $company->id)
                ->where('gateway_id', $gateway->id)
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json($withdrawals, 200);
        }
        catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar dados'
            ], 400);
        }
    }
}
