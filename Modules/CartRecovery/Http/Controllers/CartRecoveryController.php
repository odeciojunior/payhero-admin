<?php

namespace Modules\CartRecovery\Http\Controllers;

use App\Entities\Project;
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

    public function getAbandonatedCarts(Request $request)
    {
        $client = $request->input('client');
        //                        dd($request->all());
        $userProjects = UserProject::where([
                                               ['user', \Auth::user()->id],
                                               ['type', 'producer'],
                                           ])->pluck('project')->toArray();

        $abandonedCarts = Checkout::whereIn('status', ['abandoned cart', 'recovered']);

        //        if ($request->has('client') && $request->input('client') != '') {
        //
        //            $abandonedCarts->leftJoin('logs as log', function($join) use ($client) {
        //                //                $join->where('checkouts.id_log_session', '=', 'log.id_log_session');
        //                $join->where('log.name', 'like', '%' . $client . '%');
        //            })->get();
        //            dd($abandonedCarts[0]->name);
        //        }
        if ($request->has('project') && $request->input('project') != '') {
            $abandonedCarts->where('project', $request->input('project'));
        } else {
            $abandonedCarts->whereIn('project', $userProjects)->with(['projectModel']);
        }
        if ($request->has('start_date') && $request->input('start_date') != '' && $request->has('end_date') && $request->input('end_date') != '') {
            $abandonedCarts->whereBetween('created_at', [$request->input('start_date'), date('Y-m-d', strtotime($request->input('end_date') . ' + 1 day'))]);
        } else {
            if ($request->input('start_date') != '') {
                $abandonedCarts->whereDate('created_at', '>=', $request->input('start_date'));
            }

            if ($request->has('end_date') && $request->input('end_date') != '') {
                $abandonedCarts->whereDate('created_at', '<', date('Y-m-d', strtotime($request->input('end_date') . ' + 1 day')));
            }
        }
        $abandonedCarts->orderBy('id', 'DESC');

        return CartRecoveryResource::collection($abandonedCarts->paginate(10));
    }
}
