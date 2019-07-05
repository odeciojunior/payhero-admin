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
use Vinkla\Hashids\Facades\Hashids;
use App\Entities\Log as CheckoutLog;
use Yajra\DataTables\Facades\DataTables;
use Modules\CartRecovery\Transformers\CartRecoveryResource;
use Modules\CartRecovery\Transformers\CarrinhosAbandonadosResource;
use Illuminate\Support\Facades\Log;

class CartRecoveryController extends Controller
{
    /**
     * @var Checkout
     */
    private $checkoutModel;
    /**
     * @var \App\Entities\Log
     */
    private $logModel;
    /**
     * @var CheckoutPlan
     */
    private $checkoutPlansModel;
    /**
     * @var Domain
     */
    private $domainModel;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCheckoutModel()
    {
        if (!$this->checkoutModel) {
            $this->checkoutModel = app(Checkout::class);
        }

        return $this->checkoutModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getLogModel()
    {
        if (!$this->logModel) {
            $this->logModel = app(CheckoutLog::class);
        }

        return $this->logModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getCheckoutPlansModel()
    {
        if (!$this->checkoutPlansModel) {
            $this->checkoutPlansModel = app(CheckoutPlan::class);
        }

        return $this->checkoutPlansModel;
    }

    /**
     * @return Domain|\Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getDomainModel()
    {
        if (!$this->domainModel) {
            $this->domainModel = app(Domain::class);
        }

        return $this->domainModel;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getAbandonatedCardsDetails(Request $request)
    {
        try {
            if ($request->input('checkout')) {
                $checkoutId = current(Hashids::decode($request->input('checkout')));
                $checkout   = $this->getCheckoutModel()->find($checkoutId);

                $log          = $this->getLogModel()->where('id_log_session', $checkout->id_log_session)
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

                $checkoutPlans = $this->getCheckoutPlansModel()->with('plan', 'plan.products')
                                      ->where('checkout', $checkoutId)
                                      ->get();

                $plans         = [];
                $total         = 0;
                foreach ($checkoutPlans as $checkoutPlan) {
                    foreach ($checkoutPlan->getRelation('plan')->products as $key => $product) {
                        $plans[$key]['name']   = $checkoutPlan->getRelation('plan')->name;
                        $plans[$key]['value']  = $checkoutPlan->getRelation('plan')->price;
                        $plans[$key]['photo']  = $product->photo;
                        $plans[$key]['amount'] = $checkoutPlan->amount;
                        $total                 += intval(preg_replace("/[^0-9]/", "", $checkoutPlan->getRelation('plan')->price)) * intval($checkoutPlan->amount);
                    }
                }

                $domain = $this->getDomainModel()->where([['status', 3], ['project_id', $checkout->project]])->first();
                $link   = "https://checkout." . $domain->name . "/recovery/" . $checkout->id_log_session;

                $details = view('cartrecovery::details', [
                    'checkout'      => $checkout,
                    'log'           => $log,
                    'whatsapp_link' => "https://api.whatsapp.com/send?phone=55" . preg_replace('/[^0-9]/', '', $log->telephone),
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

            return redirect()->back();
        }
    }
}


