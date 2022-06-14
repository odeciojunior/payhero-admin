<?php

namespace Modules\Mobile\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\CompanyBalanceService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\UserService;
use Modules\Withdrawals\Services\WithdrawalService;

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

            if(!Gate::allows('edit', [$company])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sem permissão para visualizar saques'
                ], 403);
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

    /**
     * Returns the user's withdrawals.
     *
     * @return JsonResponse
     */
    public function withdrawalsStore(Request $request)
    {
        try {
            $companyId = hashids_decode($request->company);
            $gatewayId = hashids_decode($request->gateway);

            $company = Company::find($companyId);
            $gateway = Gateway::find($gatewayId);

            $settingsWithdrawalRequest = settings()->group('withdrawal_request')->get('withdrawal_request', null, true);

            if($settingsWithdrawalRequest != null && $settingsWithdrawalRequest == false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tente novamente em alguns minutos'
                ], 400);
            }

            if((new UserService())->userWithdrawalBlocked(auth()->user())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sem permissão para realizar saques'
                ], 403);
            }

            if(empty($company)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Empresa não encontrada'
                ], 400);
            }

            if(!Gate::allows('edit', [$company])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sem permissão para realizar saques'
                ], 403);
            }

            if(!(new WithdrawalService)->companyCanWithdraw($company->id, $gateway->id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Você só pode fazer 3 pedidos de saque por dia'
                ], 403);
            }

            $gatewayService = Gateway::getServiceById($gateway->id)->setCompany($company);

            $withdrawalValue = (int) FoxUtils::onlyNumbers($request->withdrawal_value);

            if(!$gatewayService->withdrawalValueIsValid($withdrawalValue)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Valor informado inválido'
                ], 400);
            }

            if(!$gatewayService->existsBankAccountApproved()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cadastre um meio de recebimento para solicitar saques'
                ], 400);
            }

            $responseCreateWithdrawal = $gatewayService->createWithdrawal($withdrawalValue);

            if($responseCreateWithdrawal) {
                return response()->json([
                    'message' => 'Saque em processamento'
                ], 200);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ocorreu um erro, tente novamente mais tarde!'
                ], 403);
            }
        }
        catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocorreu um erro, tente novamente mais tarde!'
            ], 400);
        }
    }
}
