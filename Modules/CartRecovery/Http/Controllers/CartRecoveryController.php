<?php

namespace Modules\CartRecovery\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Entities\Log;
use App\Entities\Plan;
use App\Entities\Domain;
use App\Entities\Project;
use App\Entities\Checkout;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use App\Entities\CheckoutPlan;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\CartRecovery\Transformers\CartRecoveryResource;
use Modules\CartRecovery\Transformers\CarrinhosAbandonadosResource;

class CartRecoveryController extends Controller
{
    public function index()
    {
        $userProjects = UserProject::where('user', \Auth::user()->id)->get()->toArray();
        $projects     = [];

        foreach ($userProjects as $userProject) {
            $project = Project::find($userProject['project']);
            if ($project['id'] != null) {
                $projects[] = [
                    'id'   => $project['id'],
                    'nome' => $project['name'],
                ];
            }
        }

        return view('cartrecovery::index', compact('projects'));
    }
 
    public function getAbandonatedCarts(Request $request) {

        try{
            $abandonedCarts = Checkout::whereIn('status', ['abandoned cart', 'recovered']);

            if ($request->has('project') && $request->input('project') != '') {
                $abandonedCarts->where('project', $request->input('project'));
            } else {
                $userProjects = UserProject::where([
                    ['user', \Auth::user()->id],
                    ['type', 'producer'],
                ])->pluck('project')->toArray();

                $abandonedCarts->whereIn('project', $userProjects)->with(['projectModel']);
            }

            if ($request->start_date != '' && $request->end_date != '') {
                $abandonedCarts->whereBetween('created_at', [$request->start_date, date('Y-m-d', strtotime($request->end_date . ' + 1 day'))]);
            } else {
                if ($request->start_date != '') {
                    $abandonedCarts->whereDate('created_at', '>=', $request->start_date);
                }

                if ($request->end_date != '') {
                    $abandonedCarts->whereDate('created_at', '<', date('Y-m-d', strtotime($request->end_date . ' + 1 day')));
                }
            }

            $abandonedCarts->orderBy('id', 'DESC');

            return CartRecoveryResource::collection($abandonedCarts->paginate(10));
        }
        catch(Exception $e){
            dd($e);
        }
    }


}


