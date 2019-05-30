<?php

namespace Modules\Sales\Http\Controllers;

use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Sale;
use App\Entities\Client;
use App\Entities\Project;
use App\Entities\Delivery;
use App\Entities\PlanSale;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Sales\Transformers\SalesResource;

class SalesController extends Controller {


    public function index() {

        $userProjects = UserProject::where('user', \Auth::user()->id)->get()->toArray();
        $projects = [];

        foreach($userProjects as $userProject){
            $project = Project::find($userProject['project']);
            $projects[] = [
                'id' => $project['id'],
                'nome' => $project['name']
            ];
        }

        return view('sales::index',[
            'projetos' => $projects,
        ]);
    }

    public function getSaleDetail(Request $request){

        $requestData = $request->all();
        $sale = Sale::find(preg_replace("/[^0-9]/", "", $requestData['sale_id']));

        $plansSales = PlanSale::where('sale', $sale->id)->get()->toArray();
        $plans = [];

        foreach($plansSales as $key => $planSale){
            $plans[$key]['name'] = Plan::find($planSale['plan'])['name'];
            $plans[$key]['amount'] = $planSale['amount'];
        }

        $client = Client::find($sale->client);
        $delivery = Delivery::find($sale->delivery);

        $sale['start_date'] = (new Carbon($sale['start_date']))->format('d/m/Y H:i:s');

        $details = view('sales::details',[
            'sale'     => $sale,
            'plans'    => $plans,
            'client'   => $client,
            'delivery' => $delivery
        ]);

        return response()->json($details->render());

    }

    public function refundSale(Request $request){

        return response()->json('sucesso');
    }

    public function getSales(Request $request){

        //$sales = Sale::where('owner',\Auth::user()->id)->orWhere('affiliate',\Auth::user()->id);
        $sales = Sale::where('owner',\Auth::user()->id);

        $sales = $sales->where('gateway_status','!=', 'refused');

        if($request->projeto != ''){
            $plans = Plan::where('project',$request->projeto)->pluck('id');
            $salePlan = PlanSale::whereIn('plan',$plans)->pluck('sale');
            $sales->whereIn('id',$salePlan);
        }

        if($request->comprador != ''){
            $clientes = Client::where('name','LIKE','%'.$request->comprador.'%')->pluck('id');
            $sales->whereIn('client',$clientes);
        }

        if($request->forma != ''){
            $sales->where('payment_form',$request->forma);
        }
        
        if($request->status != ''){
            $sales->where('gateway_status',$request->status);
        }

        if($request->data_inicial != '' && $request->data_final != ''){
            $sales->whereBetween('start_date', [$request->data_inicial,date('Y-m-d', strtotime($request->data_final.' + 1 day'))]);
        }
        else{
            if($request->data_inicial != ''){
                $sales->whereDate('start_date', '>=', $request->data_inicial);
            }

            if($request->data_final != ''){
                $sales->whereDate('end_date', '<', date('Y-m-d', strtotime($request->data_final.' + 1 day')));
            }
        }

        $sales->orderBy('id','DESC');

        return SalesResource::collection($sales->paginate(10));
    }


}
