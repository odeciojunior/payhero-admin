<?php

namespace Modules\CartRecovery\Http\Controllers;
 
use Carbon\Carbon;
use App\Entities\Log;
use App\Entities\Plan;
use App\Entities\Domain;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Entities\CheckoutPlan;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\CartRecovery\Transformers\CarrinhosAbandonadosResource;

class CartRecoveryController extends Controller {


    public function index() {

        return view('cartrecovery::index');
    }

    public function cartRecoveryData(Request $request){

        $checkouts = \DB::table('checkouts as checkout')
        ->select([
            'checkout.id',
            'checkout.status',
            'checkout.id_log_session',
            'checkout.created_at',
            'checkout.project',
        ])
        ->where('status','Carrinho abandonado')
        ->orWhere('status', 'Recuperado')
        ->orderBy('id','DESC');
 
        return Datatables::of($checkouts)
        ->editColumn('created_at', function ($checkout) {
            return with(new Carbon($checkout->created_at))->format('d/m/Y H:i:s');
        })
        ->addColumn('client', function ($checkout) {
            $log = Log::where('id_log_session', $checkout->id_log_session)->orderBy('id','DESC')->first();
            if($log)
                return $log->name;
            return '';
        })
        ->addColumn('email_status', function ($checkout) {
            return "Não enviado";
        })
        ->addColumn('sms_status', function ($checkout) {
            return "Não enviado";
        })
        ->addColumn('recovery_status', function ($checkout) {
            if($checkout->status == 'Carrinho abandonado'){
                return "<span class='badge badge-danger'>Não recuperado</span>";
            }
            else{
                return "<span class='badge badge-success'>Recuperado</span>";
            }
        })
        ->addColumn('value', function ($checkout) {
            $value = 0;
            $checkoutPlans = CheckoutPlan::where('checkout',$checkout->id)->get()->toArray();
            foreach($checkoutPlans as $checkoutPlan){
                $plan = Plan::find($checkoutPlan['plan']);
                $value += str_replace('.','',$plan['price']) * $checkoutPlan['amount'];
            }
            return substr_replace($value, '.',strlen($value) - 2, 0 );;
        })
        ->addColumn('link', function ($checkout) {

            $domain = Domain::where('project',$checkout->project)->first();

            return "https://checkout.".$domain['name']."/carrinho/".$checkout->id_log_session;
        })
        ->rawColumns(['detalhes','recovery_status'])
        ->make(true);
    }

}
