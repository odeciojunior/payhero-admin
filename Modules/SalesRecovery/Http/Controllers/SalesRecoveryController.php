<?php

namespace Modules\SalesRecovery\Http\Controllers;

use App\Entities\Checkout;
use App\Entities\Project;
use App\Entities\UserProject;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\SalesRecoveryService;
use Illuminate\View\View;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;


/**
 * Class SalesRecoveryController
 * @package Modules\SalesRecovery\Http\Controllers
 */
class SalesRecoveryController extends Controller
{
    /**
     * @return Factory|View
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
     * @return AnonymousResourceCollection
     */
    public function getRecoveryData(Request $request)
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

            return $salesRecoveryService->verifyType($request->input('type'), $projectId, $request->start_date, $endDate);
        } catch
        (Exception $e) {
            Log::warning('Erro ao buscar dados de recuperação de vendas');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function getDetails(Request $request)
    {
        try {
            $checkoutModel        = new Checkout();
            $salesRecoveryService = new SalesRecoveryService();
            $details              = null;

            if ($request->has('checkout') && !empty($request->input('checkout'))) {
                $checkoutId = current(Hashids::decode($request->input('checkout')));
                $checkout   = $checkoutModel->find($checkoutId);
                if (!empty($checkout)) {
                    $details = $salesRecoveryService->getSalesCheckoutDetails($checkout);
                } else {
                    $details = $salesRecoveryService->getSalesCartOrBoletoDetails($checkoutId);
                }

                if ($details == null) {
                    return response()->json(['message' => 'Ocorreu algum erro']);
                } else {
                    return response()->json($details->render());
                }
            } else {
                return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde']);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar detalhes do carrinho abandonado');
            report($e);

            return response()->json(['message' => 'Ocorreu algum erro, tente novamente mais tarde']);
        }
    }
}


