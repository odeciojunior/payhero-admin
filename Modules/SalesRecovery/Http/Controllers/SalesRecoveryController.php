<?php

namespace Modules\SalesRecovery\Http\Controllers;

use App\Entities\Checkout;
use App\Entities\CheckoutPlan;
use App\Entities\Domain;
use App\Entities\Log as CheckoutLog;
use App\Entities\Project;
use App\Entities\UserProject;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\SalesRecovery\SalesRecoveryService;
use Modules\SalesRecovery\Transformers\SalesRecoveryResource;
use Vinkla\Hashids\Facades\Hashids;

//use Modules\SalesRecovery\Transformers\CarrinhosAbandonadosResource;

/**
 * Class SalesRecoveryController
 * @package Modules\SalesRecovery\Http\Controllers
 */
class SalesRecoveryController extends Controller
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
            if (!empty($project['id'])) {
                $projects[] = [
                    'id'   => $project['id'],
                    'nome' => $project['name'],
                ];
            }
        }

        return view('salesrecovery::index', compact('projects'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAbandonatedCarts(Request $request)
    {

        try {
            $salesRecoveryService = new SalesRecoveryService();

            $projectId = null;
            if (!empty($request->project)) {
                $projectId = current(Hashids::decode($request->input('project')));
            }

            $endDate = null;
            if (!empty($request->end_date)) {
                $endDate = date('Y-m-d', strtotime($request->end_date . ' + 1 day'));
            }

            $abandonedCarts = $salesRecoveryService->verifyType($request->input('type'), $projectId, $request->start_date, $endDate);

            return $abandonedCarts;
        } catch
        (Exception $e) {
            Log::warning('Erro ao buscar dados de recuperação de vendas');
            report($e);
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

                $link = "https://checkout." . $domain->name . "/recovery/" . $checkout->id_log_session;

                $whatsAppMsg = 'Olá ' . $log->name;

                $details = view('salesrecovery::details', [
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


