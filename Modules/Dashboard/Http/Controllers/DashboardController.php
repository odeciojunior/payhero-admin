<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Pusher\Pusher;
use PagarMe\Client;
use App\Entities\Plan;
use App\Entities\Sale;
use Cknow\Money\Money;
use App\Entities\Company;
use App\Entities\Project;
use App\Entities\Checkout;
use App\Entities\PlanSale;
use Illuminate\Http\Request;
use App\Entities\Transaction;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

/**
 * Class DashboardController
 * @package Modules\Dashboard\Http\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $companyModel = new Company();

        $companies = $companyModel->where('user_id', auth()->user()->id)->get()->toArray();

        return view('dashboard::dashboard', [
            'companies' => $companies,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getValues(Request $request)
    {
        $companyModel     = new Company();
        $transactionModel = new Transaction();

        $requestData = $request->all();

        $antecipableBalance = 0;
        $pendingBalance     = 0;

        $company = $companyModel->find($request->company);

        $pendingTransactions = $transactionModel->where('company', $request->company)
                                                ->where('status', 'paid')
                                                ->whereDate('release_date', '>', Carbon::today()->toDateString())
                                                ->get()->toArray();

        if (count($pendingTransactions)) {
            foreach ($pendingTransactions as $pendingTransaction) {
                $pendingBalance += $pendingTransaction['value'];
            }
        }

        $anticipatedTransactions = $transactionModel->where('company', $request->company)
                                                    ->where('status', 'anticipated')
                                                    ->whereDate('release_date', '>', Carbon::today()->toDateString())
                                                    ->get()->toArray();

        if (count($anticipatedTransactions)) {
            foreach ($anticipatedTransactions as $anticipatedTransaction) {
                $pendingBalance += $anticipatedTransaction['value'] - $anticipatedTransaction['antecipable_value'];
            }
        }

        $antecipableTransactions = $transactionModel->where('company', $request->company)
                                                    ->where('status', 'paid')
                                                    ->whereDate('release_date', '>', Carbon::today())
                                                    ->whereDate('antecipation_date', '<=', Carbon::today())
                                                    ->get()->toArray();

        if (count($antecipableTransactions)) {
            foreach ($antecipableTransactions as $antecipableTransaction) {
                $antecipableBalance += $antecipableTransaction['antecipable_value'];
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
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function lastSales(Request $request)
    {

        $saleModel     = new Sale();
        $planSaleModel = new PlanSale();
        $planModel     = new Plan();
        $projectModel  = new Project();

        $requestData = $request->all();

        $sales = $saleModel->select('id', 'start_date', 'total_paid_value', 'payment_form', 'ip')
                           ->where([
                                       ['owner', auth()->user()->id],
                                       ['gateway_status', '!=', 'refused'],
                                   ])->orderBy('id', 'DESC')
                           ->limit(10)->get()->toArray();

        foreach ($sales as &$sale) {

            $planSale = $planSaleModel->where('sale', $sale['id'])->first();

            $plan = $planModel->find($planSale->plan);

            $project = $projectModel->find($plan['project']);

            $sale['project'] = $project['name'];

            $sale['start_date'] = (new Carbon($sale['start_date']))->format('d/m/Y H:i:s');
        }

        return response()->json($sales);
    }
}

