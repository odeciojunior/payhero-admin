<?php

namespace Modules\CartRecovery\Http\Controllers;

use Carbon\Carbon;
use App\Entities\Log;
use App\Entities\Plan;
use App\Entities\Domain;
use App\Entities\Checkout;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use App\Entities\CheckoutPlan;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\CartRecovery\Transformers\CartRecoveryResource;
use Modules\CartRecovery\Transformers\CarrinhosAbandonadosResource;

class CartRecoveryController extends Controller {


    public function index() {

        return view('cartrecovery::index');
    }
 
    public function getAbandonatedCarts(Request $request){

        $userProjects = UserProject::where([
            ['user', \Auth::user()->id],
            ['type','producer']
        ])->pluck('project')->toArray();

        $abandonedCarts = Checkout::where('status','abandoned cart')
                                  ->orWhere('status','recovered')
                                  ->whereIn('project',$userProjects)
                                  ->orderBy('id','DESC');

        return CartRecoveryResource::collection($abandonedCarts->paginate(10));

    }

}
