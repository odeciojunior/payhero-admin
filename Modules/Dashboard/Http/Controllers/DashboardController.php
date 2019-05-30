<?php

namespace Modules\Dashboard\Http\Controllers;

use Carbon\Carbon;
use Pusher\Pusher;
use PagarMe\Client;
use App\Entities\Plan;
use App\Entities\Sale;
use App\Entities\Company;
use App\Entities\Project;
use App\Entities\PlanSale;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use App\Entities\Transaction;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Entities\Checkout;

class DashboardController extends Controller {

    public function index() {

        $availableBalance = 0;
        $futureBalance    = 0;
        $dailyBalance     = 0;

        $companies = Company::where('user',\Auth::user()->id)->get()->toArray();

        foreach($companies as $company){

            $pendingTransactions = Transaction::where('company',$company['id'])
                ->where('status','paid')
                ->whereDate('release_date', '>', Carbon::today()->toDateString())
                ->get()->toArray();

            if(count($pendingTransactions)){
                foreach($pendingTransactions as $pendingTransaction){
                    $futureBalance += $pendingTransaction['value'];
                }
            }

            $todayTransactions = Transaction::where('company',$company['id'])
                ->whereDate('created_at', Carbon::today())
                ->get()->toArray();

            if(count($todayTransactions)){
                foreach($todayTransactions as $todayTransaction){
                    $dailyBalance += $todayTransaction['value'];
                }
            }
                    
        }

        if($availableBalance == 0){
            $availableBalance = '000';
        }
        if($futureBalance == 0){
            $futureBalance = '000';
        }

        $availableBalance = \Auth::user()->saldo;
        $availableBalance = number_format($availableBalance,2);
        $futureBalance    = substr_replace($futureBalance, '.',strlen($futureBalance) - 2, 0 );
        $futureBalance    = number_format($futureBalance,2);
        $dailyBalance     = substr_replace($dailyBalance, '.',strlen($dailyBalance) - 2, 0 );
        $dailyBalance     = number_format($dailyBalance,2);

        $userProjects = UserProject::where([
            ['user', \Auth::user()->id],
            ['type','producer']
        ])->pluck('project')->toArray();

        $salesCount = Sale::whereIn('project',$userProjects)->whereDate('start_date', Carbon::today())->count();
        $checkouts  = Checkout::whereIn('project',$userProjects)->whereDate('created_at', Carbon::today())->count();

        return view('dashboard::dashboard',[
            'available_balance' => $availableBalance,
            'future_balance'    => $futureBalance,
            'sales_count'       => $salesCount,
            'daily_balance'     => $dailyBalance,
            'checkouts'         => $checkouts,
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
