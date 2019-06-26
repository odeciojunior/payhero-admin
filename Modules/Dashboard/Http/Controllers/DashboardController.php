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

class DashboardController extends Controller {

    public function index() {

        $companies = Company::where('user_id',\Auth::user()->id)->get()->toArray();

        return view('dashboard::dashboard',[
            'companies' => $companies,
        ]);
    }

    public function getValues(Request $request){

        $requestData = $request->all();

        $antecipableBalance = 0;
        $pendingBalance     = 0;

        $company = Company::find($request->company);

        $pendingTransactions = Transaction::where('company',$request->company)
            ->where('status','paid')
            ->whereDate('release_date', '>', Carbon::today()->toDateString())
            ->get()->toArray();

        if(count($pendingTransactions)){
            foreach($pendingTransactions as $pendingTransaction){
                $pendingBalance += $pendingTransaction['value'];
            }
        }

        $antecipableTransactions = Transaction::where('company',$request->company)
            ->whereDate('release_date', '>', Carbon::today())
            ->whereDate('antecipation_date', '<=', Carbon::today())
            ->get()->toArray();

        if(count($antecipableTransactions)){
            foreach($antecipableTransactions as $antecipableTransaction){
                $antecipableBalance += $antecipableTransaction['antecipable_value'];
            }
        }

        $availableBalance   = $company->balance;
        $totalBalance       = $availableBalance + $pendingBalance;

        return response()->json([
            'available_balance'   => number_format(intval($availableBalance) / 100, 2, ',', '.'),
            'antecipable_balance' => number_format(intval($antecipableBalance) / 100, 2, ',', '.'),
            'total_balance'       => number_format(intval($totalBalance) / 100, 2, ',', '.'),
            'pending_balance'     => number_format(intval($pendingBalance) / 100, 2, ',', '.'),
            'currency'            => $company->country == 'usa' ? '$' : 'R$',
        ]);

    }

    public function lastSales(Request $request){
 
        $requestData = $request->all();

        $sales = Sale::select('id','start_date','total_paid_value','payment_form','ip')
        ->where([
            [ 'owner', \Auth::user()->id ],
            [ 'gateway_status', '!=', 'refused']
        ])->orderBy('id', 'DESC')
        ->limit(10)->get()->toArray();

        foreach($sales as &$sale){

            $planSale = PlanSale::where('sale',$sale['id'])->first();

            $plan = Plan::find($planSale->plan);

            $project = Project::find($plan['project']);

            $sale['project'] = $project['name'];

            $sale['start_date'] = (new Carbon($sale['start_date']))->format('d/m/Y H:i:s');
        }

        return response()->json($sales);
    }

}

