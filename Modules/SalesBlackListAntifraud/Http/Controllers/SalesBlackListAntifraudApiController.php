<?php

namespace Modules\SalesBlackListAntifraud\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Modules\Sales\Transformers\SalesResource;
use Modules\SalesBlackListAntifraud\Transformers\SalesBlackListAntiFraudDetaislResource;
use Modules\SalesBlackListAntifraud\Transformers\SalesBlackListAntiFraudResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SalesBlackListAntifraudApiController
 * @package Modules\SalesBlackListAntifraud\Http\Controllers
 */
class SalesBlackListAntifraudApiController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            activity()->tap(function(Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela BlackList e AntiFraud');

            $filters = $request->all();

            $companyModel = new Company();
            $saleModel    = new Sale();

            $userId = auth()->user()->account_owner_id;

            $userCompanies = $companyModel->where('user_id', $userId)
                                          ->pluck('id')
                                          ->toArray();
            $sales         = $saleModel
                ->with([
                           'customer', 'plansSales', 'plansSales.plan', 'plansSales.plan.products',
                           'plansSales.plan.project',
                           'project',
                           'saleWhiteBlackListResult',
                           'transactions' => function($query) use ($userCompanies) {
                               $query->whereIn('company_id', $userCompanies);
                           },
                       ])->whereIn('status', [10, 21]);

            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            if ($dateRange[0] < '2020-04-10') {
                $dateRange[0] = '2020-04-10';
            }
            $sales->whereBetween('start_date', [$dateRange[0], $dateRange[1] . ' 23:59:59']);

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $sales->where('project_id', $projectId);
            } else {
                $userProjects = UserProject::where('user_id', $userId)->pluck('project_id');
                $sales->whereIn('project_id', $userProjects);
            }

            if (!empty($filters["transaction"])) {
                $saleId = current(Hashids::connection('sale_id')
                                         ->decode(str_replace('#', '', $filters["transaction"])));
                $sales->where('id', $saleId);
            }

            return SalesBlackListAntiFraudResource::collection($sales->orderBy('start_date', 'DESC')->paginate(10));
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao carregar',], 400);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse|SalesBlackListAntiFraudDetaislResource
     */
    public function show(Request $request, $id)
    {
        try {
            $saleModel = new Sale();
            if (!empty($id)) {
                $sale = $saleModel->find(current(Hashids::connection('sale_id')->decode($id)));

                return new SalesBlackListAntiFraudDetaislResource($sale);
            } else {
                return response()->json([
                                            'message' => 'Ocorreu um erro, tente novamente!',
                                        ], 400);
            }
        } catch (Exception $e) {
            report($e);

            return response()->json([
                                        'message' => 'Ocorreu um erro, tente novamente!',
                                    ], 400);
        }
    }
}
