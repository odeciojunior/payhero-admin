<?php

namespace Modules\SalesRecovery\Http\Controllers;

use App\Entities\Checkout;
use App\Entities\CheckoutPlan;
use App\Entities\Domain;
use App\Entities\Log as CheckoutLog;
use App\Entities\Project;
use App\Entities\Sale;
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
            $checkoutModel = new Checkout();

            $saleModel            = new Sale();
            $salesRecoveryService = new SalesRecoveryService();
            $details              = null;

            if ($request->has('checkout') && !empty($request->input('checkout'))) {
                $checkoutId = current(Hashids::decode($request->input('checkout')));

                $checkout = $checkoutModel->find($checkoutId);
                if (!empty($checkout)) {
                    $details = $salesRecoveryService->getSalesCheckoutDetails($checkout);
                } else {

                    $details = $salesRecoveryService->getSalesCartOrBoletoDetails($checkoutId);
                }
            }

            if ($details == null) {
                return response()->json(['message' => 'Ocorreu algum erro']);
            }

            return response()->json($details->render());
        } catch (Exception $e) {
            Log::warning('Erro ao buscar detalhes do carrinho abandonado');
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro']);
        }
    }
}


