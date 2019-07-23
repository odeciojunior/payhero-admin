<?php

namespace Modules\CartRecovery\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Entities\Plan;
use App\Entities\Domain;
use App\Entities\Project;
use App\Entities\Checkout;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Http\Response;
use App\Entities\CheckoutPlan;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use App\Entities\Log as CheckoutLog;
use Yajra\DataTables\Facades\DataTables;
use Modules\CartRecovery\Transformers\CartRecoveryResource;
use Modules\CartRecovery\Transformers\CarrinhosAbandonadosResource;

/**
 * Class CartRecoveryController
 * @package Modules\CartRecovery\Http\Controllers
 */
class CartRecoveryController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $userProjectModel = new UserProject();
        $projectModel     = new Project();

        $userProjects = $userProjectModel->where('user', auth()->user()->id)->get()->toArray();
        $projects     = [];

        foreach ($userProjects as $userProject) {
            $project = $projectModel->find($userProject['project']);
            if ($project['id'] != null) {
                $projects[] = [
                    'id'   => $project['id'],
                    'nome' => $project['name'],
                ];
            }
        }

        return view('cartrecovery::index', compact('projects'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAbandonatedCarts(Request $request)
    {

        try {
            $checkoutModel    = new Checkout();
            $userProjectModel = new UserProject();

            $abandonedCarts = $checkoutModel->whereIn('status', ['abandoned cart', 'recovered']);

            if ($request->has('project') && $request->input('project') != '') {
                $abandonedCarts->where('project', $request->input('project'));
            } else {
                $userProjects = $userProjectModel->where([
                                                             ['user', auth()->user()->id],
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
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getAbandonatedCardsDetails(Request $request)
    {
        try {
            $checkoutModel     = new Checkout();
            $logModel          = new CheckoutLog();
            $checkoutPlanModel = new CheckoutPlan();
            $domainModel       = new Domain();

            if ($request->input('checkout')) {
                $checkoutId = current(Hashids::decode($request->input('checkout')));
                $checkout   = $checkoutModel->find($checkoutId);

                $log          = $logModel->where('id_log_session', $checkout->id_log_session)
                                         ->orderBy('id', 'DESC')
                                         ->first();
                $log['hours'] = with(new Carbon($checkout->created_at))->format('H:i:s');
                $log['date']  = with(new Carbon($checkout->created_at))->format('d/m/Y');

                $status = '';
                if ($checkout->status == 'abandoned cart') {
                    $status = 'Não recuperado';
                } else {
                    $status = 'Recuperado';
                }

                $checkoutPlans = $checkoutPlanModel->with('plan', 'plan.products')
                                                   ->where('checkout', $checkoutId)
                                                   ->get();

                $plans = [];
                $total = 0;
                foreach ($checkoutPlans as $checkoutPlan) {
                    foreach ($checkoutPlan->getRelation('plan')->products as $key => $product) {
                        $plans[$key]['name']   = $checkoutPlan->getRelation('plan')->name;
                        $plans[$key]['value']  = $checkoutPlan->getRelation('plan')->price;
                        $plans[$key]['photo']  = $product->photo;
                        $plans[$key]['amount'] = $checkoutPlan->amount;
                        $total                 += intval(preg_replace("/[^0-9]/", "", $checkoutPlan->getRelation('plan')->price)) * intval($checkoutPlan->amount);
                    }
                }

                $domain = $domainModel->where([['status', 3], ['project_id', $checkout->project]])->first();

                $link   = "https://checkout." . $domain->name . "/recovery/" . $checkout->id_log_session;

                $whatsAppMsg = 'Olá ' . $log->name;

                $details = view('cartrecovery::details', [
                    'checkout'      => $checkout,
                    'log'           => $log,
                    'whatsapp_link' => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $log->telephone) . '&text=' . $whatsAppMsg,
                    'status'        => $status,
                    'hours'         => $log['hours'],
                    'date'          => $log['date'],
                    'plans'         => $plans,
                    'total'         => number_format(intval($total) / 100, 2, ',', '.'),
                    'link'          => $link,
                ]);
            }
            return response()->json($details->render());
        } catch (Exception $e) {
            Log::warning('Erro ao buscar detalhes do carrinho abandonado');
            report($e);
            return response()->json(['message' => 'Ocorreu algum erro']);
        }
    }
}


