<?php

namespace Modules\SalesBlackListAntifraud\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Customer;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\PlanSale;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Modules\Sales\Transformers\TransactionResource;
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

            $saleService = new SaleService();

            $filters = $request->all();

            $filters['shopify_error'] = '0';

            $companyModel     = new Company();
            $customerModel    = new Customer();
            $transactionModel = new Transaction();

            $userId = auth()->user()->account_owner_id;

            $userCompanies = $companyModel->where('user_id', $userId)
                                          ->pluck('id')
                                          ->toArray();

            $transactions = $transactionModel->with([
                                                        'sale',
                                                        'sale.project',
                                                        'sale.customer',
                                                        'sale.plansSales.plan.productsPlans.product',
                                                        'sale.shipping',
                                                        'sale.checkout',
                                                        'sale.delivery',
                                                        'sale.transactions',
                                                        'sale.affiliate.user',
                                                    ])->whereIn('company_id', $userCompanies)
                                             ->join('sales', 'sales.id', 'transactions.sale_id')
                                             ->whereNull('invitation_id');

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $transactions->whereHas('sale', function($querySale) use ($projectId) {
                    $querySale->where('project_id', $projectId);
                });
            }

            if (!empty($filters["transaction"])) {
                $saleId = current(Hashids::connection('sale_id')
                                         ->decode(str_replace('#', '', $filters["transaction"])));

                $transactions->whereHas('sale', function($querySale) use ($saleId) {
                    $querySale->where('id', $saleId);
                });
            }

            if (!empty($filters["client"])) {
                $customers = $customerModel->where('name', 'LIKE', '%' . $filters["client"] . '%')->pluck('id');
                $transactions->whereHas('sale', function($querySale) use ($customers) {
                    $querySale->whereIn('customer_id', $customers);
                });
            }
            /*if (!empty($filters['shopify_error']) && $filters['shopify_error'] == true) {
                $transactions->whereHas('sale.project.shopifyIntegrations', function($queryShopifyIntegration) {
                    $queryShopifyIntegration->where('status', 2);
                });
                $transactions->whereHas('sale', function($querySaleShopify) {
                    $querySaleShopify->whereNull('shopify_order');
                });
            }*/
            if (!empty($filters["payment_method"])) {
                $method = $filters["payment_method"];
                $transactions->whereHas('sale', function($querySale) use ($method) {
                    $querySale->where('payment_method', $method);
                });
            }

            if (empty($filters['status'])) {
                $status = [10, 20];
            } else {
                $status = [$filters["status"]];
            }

            $transactions->whereHas('sale', function($querySale) use ($status) {
                $querySale->whereIn('status', $status);
            });

            //tipo da data e periodo obrigatorio
            $dateRange = FoxUtils::validateDateRange($filters["date_range"]);
            $dateType  = $filters["date_type"];

            $transactions->whereHas('sale', function($querySale) use ($dateRange, $dateType) {
                $querySale->whereBetween($dateType, [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
            })->selectRaw('transactions.*, sales.start_date')
                         ->orderByDesc('sales.start_date');

            dd($transactions->paginate(10));
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao carregar',], 400);
        }
    }

    public
    function show($id)
    {
        return view('salesblacklistantifraud::show');
    }
}
