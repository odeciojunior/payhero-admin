<?php

namespace Modules\Reports\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\FoxUtils;
use Modules\Reports\Transformers\PendingBalanceResource;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ReportService;
use Modules\Reports\Transformers\SalesByOriginResource;
use Modules\Reports\Transformers\CheckoutsByOriginResource;
use Modules\Core\Entities\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Reports\Exports\Reports\ReportExport;
use Modules\Reports\Transformers\ReportCouponResource;
use Spatie\Activitylog\Models\Activity;
use Modules\Core\Services\SaleService;
use Modules\Reports\Transformers\TransactionBlockedResource;

class ReportsApiController extends Controller
{
    public function index(request $request): JsonResponse
    {
        try {
            $userProjectModel = new UserProject();
            $salesModel = new Sale();
            $planModel = new Plan();
            $affiliateModel = new Affiliate();

            $dataSearch = $request->all();
            $projectId = current(Hashids::decode($request->input('project')));

            $requestStartDate = $request->input('startDate');
            $requestEndDate = $request->input('endDate');
            if ($projectId) {
                $userProject = $userProjectModel->where([
                    ['user_id', auth()->user()->account_owner_id],
                    ['type', 'producer'],
                    ['project_id', $projectId],
                ])->first();

                $affiliate = $affiliateModel->where([
                    ['user_id', auth()->user()->account_owner_id],
                    ['project_id', $projectId],
                ])->first();

                $itens = $salesModel
                    ->select(DB::raw('count(*) as count'), 'plan_sale.plan_id')
                    ->leftJoin('plans_sales as plan_sale', function ($join) {
                        $join->on('plan_sale.sale_id', '=', 'sales.id');
                    })
                    ->where('sales.status', 1)->where('project_id', $projectId);

                if (!empty($requestStartDate) && !empty($requestEndDate)) {
                    $itens->whereBetween('sales.start_date',
                        [$requestStartDate, date('Y-m-d', strtotime($requestEndDate.' + 1 day'))]);
                } else {
                    if (!empty($requestStartDate)) {
                        $itens->whereDate('sales.start_date', '>=', $requestStartDate);
                    }

                    if (!empty($requestEndDate)) {
                        $itens->whereDate('sales.end_date', '<', date('Y-m-d', strtotime($requestEndDate.' + 1 day')));
                    }
                }

                $itens = $itens->groupBy('plan_sale.plan_id')->orderBy('count', 'desc')->limit(5)->get()->toArray();
                $plans = [];
                foreach ($itens as $key => $iten) {
                    $plan = $planModel->with('products')->find($iten['plan_id']);
                    $plans[$key]['name'] = $plan->name.' - '.$plan->description;
                    $plans[$key]['photo'] = $plan->products[0]->photo;
                    $plans[$key]['quantidade'] = $iten['count'];
                    unset($plan);
                }

                // calculos dashboard
                $salesDetails = $salesModel->selectRaw("COUNT(*) AS contSales,
                                                        SUM(CASE WHEN sales.status = 1 THEN 1 ELSE 0 END) AS contSalesAproved,
                                                        SUM(CASE WHEN sales.status = 2 THEN 1 ELSE 0 END) AS contSalesPending,
                                                        SUM(CASE WHEN sales.status = 3 THEN 1 ELSE 0 END) AS contSalesRecused,
                                                        SUM(CASE WHEN sales.status = 4 THEN 1 ELSE 0 END) AS contSalesChargeBack,
                                                        SUM(CASE WHEN sales.status = 5 THEN 1 ELSE 0 END) AS contSalesCanceled,
                                                        SUM(CASE WHEN sales.status = 7 THEN 1 ELSE 0 END) AS contSalesRefunded,
                                                        SUM(CASE WHEN sales.status = 24 THEN 1 ELSE 0 END) AS contSalesInDispute,
                                                        SUM(CASE WHEN sales.payment_method = 1 AND sales.status = 1 THEN ((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) ELSE 0 END) AS totalValueCreditCard,
                                                        SUM(CASE WHEN sales.payment_method = 1 AND sales.status = 1 THEN 1 ELSE 0 END) AS contCreditCardAproved,
                                                        SUM(CASE WHEN sales.payment_method = 2 AND sales.status = 1 THEN ((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) ELSE 0 END) AS totalValueBoleto,
                                                        SUM(CASE WHEN sales.payment_method = 2 AND sales.status = 1 THEN 1 ELSE 0 END) AS contBoletoAproved,
                                                        SUM(CASE WHEN sales.payment_method = 4 AND sales.status = 1 THEN ((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) ELSE 0 END) AS totalValuePix,
                                                        SUM(CASE WHEN sales.payment_method = 4 AND sales.status = 1 THEN 1 ELSE 0 END) AS contPixAproved,
                                                        SUM(CASE WHEN sales.payment_method = 1 AND sales.status IN (1, 2, 3, 4, 5, 7, 24) THEN 1 ELSE 0 END) AS contCreditCard,
                                                        SUM(CASE WHEN sales.payment_method = 2 AND sales.status != 99 THEN 1 ELSE 0 END) AS contBoleto,
                                                        SUM(CASE WHEN sales.payment_method = 4 AND sales.status != 99 THEN 1 ELSE 0 END) AS contPix,
                                                        SUM(CASE WHEN sales.status = 1 THEN ((sales.sub_total + sales.shipment_value) - (ifnull(sales.shopify_discount, 0) + sales.automatic_discount) / 100) ELSE 0 END) AS totalPaidValueAproved,
                                                        SUM(CASE WHEN checkout.is_mobile = 1 THEN 1 ELSE 0 END) AS contMobile,
                                                        SUM(CASE WHEN checkout.is_mobile = 0 THEN 1 ELSE 0 END) AS contDesktop")
                    ->leftJoin('checkouts as checkout', 'sales.checkout_id', '=', 'checkout.id')
                    ->where('sales.project_id', $projectId);
                if (!empty($userProject)) {
                    $salesDetails->where('owner_id', auth()->user()->account_owner_id);
                }
                if (!empty($affiliate)) {
                    $salesDetails->where('affiliate_id', $affiliate->id);
                }
                if (!empty($requestStartDate) && !empty($requestEndDate)) {
                    $salesDetails->whereBetween('start_date',
                        [$requestStartDate.' 00:00:00', $requestEndDate.' 23:59:59']);
                }
                $salesDetails->where(function ($q1) {
                    $q1->where('sales.status', 4)->whereDoesntHave('saleLogs', function ($querySaleLog) {
                        $querySaleLog->whereIn('status_enum', collect([20, 7]));
                    })->orWhere('sales.status', '<>', 4);
                });
                $details = $salesDetails->first();
                $contSales = $details->contSales;
                $countSalesAproved = $details->contSalesAproved;
                $countSalesPending = $details->contSalesPending;
                $countSalesRecused = $details->contSalesRecused;
                $countSalesChargeBack = $details->contSalesChargeBack;
                $countSalesRefunded = $details->contSalesRefunded;
                $countSalesCanceled = $details->contSalesCanceled;
                $countSalesInDispute = $details->contSalesInDispute;
                $totalValueCreditCard = $details->totalValueCreditCard;
                $contCreditCardAproved = $details->contCreditCardAproved;
                $totalValueBoleto = $details->totalValueBoleto;
                $totalValuePix = $details->totalValuePix;
                $contBoletoAproved = $details->contBoletoAproved;
                $contPixAproved = $details->contPixAproved;
                $contCreditCard = $details->contCreditCard;
                $contBoleto = $details->contBoleto;
                $contPix = $details->contPix;
                $totalPaidValueAproved = $details->totalPaidValueAproved;
                $contMobile = $details->contMobile;
                $contDesktop = $details->contDesktop;
                $ticketMedio = 0;

                $currency = 'R$';

                $reportService = new ReportService();

                $chartData = $reportService->getChartData($dataSearch, $projectId, $currency);

                $cartaoConvert = $contCreditCardAproved.'/'.$contCreditCard;
                $boletoConvert = $contBoletoAproved.'/'.$contBoleto;
                $pixConvert = $contPixAproved.'/'.$contPix;

                if ($contBoleto != 0) {
                    $convercaoBoleto = number_format((intval($contBoletoAproved) * 100) / intval($contBoleto), 2, ',',
                        ' . ');
                }

                if ($contCreditCard != 0) {
                    $convercaoCreditCard = number_format((intval($contCreditCardAproved) * 100) / intval($contCreditCard),
                        2, ',', ' . ');
                }

                if ($contPixAproved != 0) {
                    $convercaoPix = number_format((intval($contPixAproved) * 100) / intval($contPix),
                        2, ',', ' . ');
                }

                if ($contSales > 0) {
                    $conversaoMobile = number_format((intval($contMobile) * 100) / intval($contSales), 2, ',', ' . ');
                    $conversaoDesktop = number_format((intval($contDesktop) * 100) / intval($contSales), 2, ',', ' . ');
                } else {
                    $conversaoMobile = "0.00";
                    $conversaoDesktop = "0.00";
                }

                if ($totalPaidValueAproved != 0) {
                    $totalPercentPaidCredit = number_format((intval($totalValueCreditCard) * 100) / intval($totalPaidValueAproved),
                        2, ',', ' . ');
                    $totalPercentPaidBoleto = number_format((intval($totalValueBoleto) * 100) / intval($totalPaidValueAproved),
                        2, ',', ' . ');
                    $totalPercentPaidPix = number_format((intval($totalValuePix) * 100) / intval($totalPaidValueAproved),
                                                            2, ',', ' . ');
                    $ticketMedio = number_format($totalPaidValueAproved / $countSalesAproved, 2, ',', '.');
                }
            }
            if (empty($chartData)) {
                $chartData = [
                    'label_list' => ['', ''],
                    'credit_card_data' => [0, 0],
                    'boleto_data' => [0, 0],
                    'pix_data' => [0, 0],
                    'currency' => '',
                ];
            }

            return response()->json([
                'totalPaidValueAproved' => isset($totalPaidValueAproved) ? FoxUtils::formatMoney($totalPaidValueAproved) : 00,
                'contAproved' => $countSalesAproved ?? 0,
                'contBoleto' => $contBoleto ?? 0,
                'contRecused' => $countSalesRecused ?? 0,
                'contChargeBack' => $countSalesChargeBack ?? 0,
                'contPending' => $countSalesPending ?? 0,
                'contRefunded' => $countSalesRefunded ?? 0,
                'contCanceled' => $countSalesCanceled ?? 0,
                'contInDispute' => $countSalesInDispute ?? 0,
                'totalPercentCartao' => $totalPercentPaidCredit ?? 0,
                'totalPercentPaidBoleto' => $totalPercentPaidBoleto ?? 0,
                'totalPercentPaidPix' => $totalPercentPaidPix ?? 0,
                'totalValueBoleto' => isset($totalValueBoleto) ? FoxUtils::formatMoney($totalValueBoleto) : 00,
                'totalValueCreditCard' => isset($totalValueCreditCard) ? FoxUtils::formatMoney($totalValueCreditCard) : 00,
                'totalValuePix' => isset($totalValuePix) ? FoxUtils::formatMoney($totalValuePix) : 00,
                'chartData' => $chartData,
                'currency' => $currency ?? 0,
                'convercaoBoleto' => $convercaoBoleto ?? 0,
                'convercaoCreditCard' => $convercaoCreditCard ?? 0,
                'convercaoPix' => $convercaoPix ?? 0,
                'conversaoMobile' => $conversaoMobile ?? 0,
                'conversaoDesktop' => $conversaoDesktop ?? 0,
                'cartaoConvert' => $cartaoConvert ?? 0,
                'boletoConvert' => $boletoConvert ?? 0,
                'pixConvert' => $pixConvert ?? 0,
                'plans' => $plans ?? 0,
                'ticketMedio' => $ticketMedio ?? number_format(0,2, ',', '.)'),
            ]);
        } catch (Exception $e) {
            report($e);

            return response()->json(null);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getSalesByOrigin(Request $request)
    {
        try {
            $saleModel = new Sale();
            $affiliateModel = new Affiliate();

            $userId = auth()->user()->account_owner_id;

            if (!empty($request->project_id) && $request->project_id != null && $request->project_id != 'undefined') {
                $affiliate = $affiliateModel->where([
                    ['user_id', $userId],
                    ['project_id', $request->project_id],
                ])->first();
                $orders = $saleModel
                    ->select(DB::raw('count(*) as sales_amount, SUM(transaction.value) as value, checkout.'.$request->origin.' as origin'))
                    ->leftJoin('transactions as transaction', function ($join) use ($userId) {
                        $join->on('transaction.sale_id', '=', 'sales.id');
                        $join->where('transaction.user_id', $userId);
                    })
                    ->leftJoin('checkouts as checkout', function ($join) {
                        $join->on('checkout.id', '=', 'sales.checkout_id');
                    })
                    ->where('sales.project_id', current(Hashids::decode($request->project_id)))
                    ->where('sales.status', 1)
                    ->whereBetween('start_date',
                        [$request->start_date, date('Y-m-d', strtotime($request->end_date.' + 1 day'))])
                    ->whereNotIn('checkout.'.$request->origin, ['', 'null'])
                    ->whereNotNull('checkout.'.$request->origin)
                    ->groupBy('checkout.'.$request->origin)
                    ->orderBy('sales_amount', 'DESC');
                if (!empty($affiliate)) {
                    $orders->where('sales.affiliate_id', $affiliate->id);
                }

                return SalesByOriginResource::collection($orders->paginate(6));
            }

            return response()->json('project not found');
        } catch (Exception $e) {
            Log::warning('erro na tabela de origens');

            return response()->json('Ocorreu algum erro');
        }
    }

    public function checkouts(Request $request)
    {
        try {
            $userProjectModel = new UserProject();
            $planModel = new Plan();
            $checkoutsModel = new Checkout();
            $affiliateModel = new Affiliate();

            $dataSearch = $request->all();

            $projectId = current(Hashids::decode($request->input('project')));

            $requestStartDate = $request->input('startDate');
            $requestEndDate = $request->input('endDate');
            if ($projectId) {
                $userId = auth()->user()->account_owner_id;
                $userProject = $userProjectModel->where([
                    ['user_id', $userId],
                    ['type', 'producer'],
                    ['project_id', $projectId],
                ])->first();

                $affiliate = $affiliateModel->where([
                    ['user_id', $userId],
                    ['project_id', $projectId],
                ])->first();

                if ($userProject || $affiliate) {
                    $itens = $checkoutsModel
                        ->select(\DB::raw('count(*) as count'), 'plan_checkout.plan_id')
                        ->leftJoin('checkout_plans as plan_checkout', function ($join) {
                            $join->on('plan_checkout.checkout_id', '=', 'checkouts.id');
                        })
                        ->where('project_id', $projectId);

                    if (!empty($requestStartDate) && !empty($requestEndDate)) {
                        $itens->whereBetween('checkouts.created_at',
                            [$requestStartDate, date('Y-m-d', strtotime($requestEndDate.' + 1 day'))]);
                    } else {
                        if (!empty($requestStartDate)) {
                            $itens->whereDate('checkouts.start_date', '>=', $requestStartDate);
                        }

                        if (!empty($requestEndDate)) {
                            $itens->whereDate('checkouts.start_date', '<',
                                date('Y-m-d', strtotime($requestEndDate.' + 1 day')));
                        }
                    }
                    if (!empty($affiliate)) {
                        $itens->where('affiliate_id', $affiliate->id);
                    }
                    $itens = $itens->groupBy('plan_checkout.plan_id')->orderBy('count', 'desc')->limit(5)->get()
                        ->toArray();

                    $plans = [];
                    foreach ($itens as $key => $iten) {
                        $plan = $planModel->with('products')->find($iten['plan_id']);
                        if (!FoxUtils::isEmpty($plan)){
                            if(!empty($plan->description)){
                                $plans[$key]['name'] = $plan->name.' - '.$plan->description;
                            } else{
                                $plans[$key]['name'] = $plan->name;
                            }
                            $plans[$key]['photo'] = $plan->products[0]->photo;
                            $plans[$key]['quantidade'] = $iten['count'];
                        }
                        unset($plan);
                    }

                    // calculos dashboard
                    $checkoutsDetails = $checkoutsModel->select([
                        DB::raw('SUM(CASE WHEN checkouts.status_enum = 1 THEN 1 ELSE 0 END) AS contCheckoutsAcessed'),
                        DB::raw('SUM(CASE WHEN checkouts.status_enum = 2 THEN 1 ELSE 0 END) AS contCheckoutsAbandoned'),
                        DB::raw('SUM(CASE WHEN checkouts.status_enum = 3 THEN 1 ELSE 0 END) AS contCheckoutsRecovered'),
                        DB::raw('SUM(CASE WHEN checkouts.status_enum = 4 THEN 1 ELSE 0 END) AS contCheckoutsFinalized'),
                        DB::raw('SUM(CASE WHEN checkouts.is_mobile = 0 THEN 1 ELSE 0 END) AS contCheckoutsDesktop'),
                        DB::raw('SUM(CASE WHEN checkouts.is_mobile = 1 THEN 1 ELSE 0 END) AS contCheckoutsMobile'),
                    ])
                        ->where('project_id', $projectId);
                    if ($requestStartDate != '' && $requestEndDate != '') {
                        $checkoutsDetails->whereBetween('created_at',
                            [$requestStartDate, date('Y-m-d', strtotime($requestEndDate.' + 1 day'))]);
                    } else {
                        if (!empty($requestStartDate)) {
                            $checkoutsDetails->whereDate('created_at', '>=', $requestStartDate);
                        }

                        if (!empty($requestEndDate)) {
                            $checkoutsDetails->whereDate('updated_at', '<',
                                date('Y-m-d', strtotime($requestEndDate.' + 1 day')));
                        }
                    }
                    if (!empty($affiliate)) {
                        $checkoutsDetails->where('affiliate_id', $affiliate->id);
                    }
                    $details = $checkoutsDetails->get();

                    $countCheckoutsAcessed = $details[0]->contCheckoutsAcessed;
                    $countCheckoutsAbandoned = $details[0]->contCheckoutsAbandoned;
                    $countCheckoutsRecovered = $details[0]->contCheckoutsRecovered;
                    $countCheckoutsFinalized = $details[0]->contCheckoutsFinalized;
                    $contMobile = $details[0]->contCheckoutsMobile;
                    $contDesktop = $details[0]->contCheckoutsDesktop;

                    $reportService = new ReportService();

                    $chartData = $reportService->getChartDataCheckouts($dataSearch, $projectId);

                    $contCheckouts = $contMobile + $contDesktop;
                    if ($contCheckouts > 0) {
                        $conversaoMobile = number_format((intval($contMobile) * 100) / intval($contCheckouts), 2, ',',
                            ' . ');
                        $conversaoDesktop = number_format((intval($contDesktop) * 100) / intval($contCheckouts), 2, ',',
                            ' . ');
                    } else {
                        $conversaoMobile = "0.00";
                        $conversaoDesktop = "0.00";
                    }

                    $totalCheckouts = $countCheckoutsAcessed + $countCheckoutsAbandoned + $countCheckoutsRecovered + $countCheckoutsFinalized;
                    $accessedPercentage = $totalCheckouts ? number_format(($countCheckoutsAcessed * 100) / $totalCheckouts,
                            2, ',', '.').'%' : '0,00%';
                    $finalizedPercentage = $totalCheckouts ? number_format(($countCheckoutsFinalized * 100) / $totalCheckouts,
                            2, ',', '.').'%' : '0,00%';
                    $recoveredPercentage = $countCheckoutsAbandoned ? number_format(($countCheckoutsRecovered * 100) / $countCheckoutsAbandoned,
                            2, ',', '.').'%' : '0,00%';
                    $abandonedPercentage = $totalCheckouts ? number_format(($countCheckoutsAbandoned * 100) / $totalCheckouts,
                            2, ',', '.').'%' : '0,00%';
                }
            }
            if (empty($chartData)) {
                $chartData = [
                    'label_list' => ['', ''],
                    'checkout_data' => [0, 0],
                ];
            }

            return response()->json([
                'contAcessed' => $countCheckoutsAcessed ?? 0,
                'contAbandoned' => $countCheckoutsAbandoned ?? 0,
                'contRecovered' => $countCheckoutsRecovered ?? 0,
                'contFinalized' => $countCheckoutsFinalized ?? 0,
                'contCheckouts' => $totalCheckouts ?? 0,
                'chartData' => $chartData,
                'conversaoMobile' => $conversaoMobile ?? 0,
                'conversaoDesktop' => $conversaoDesktop ?? 0,
                'plans' => $plans ?? 0,
                'percentAbandoned' => $abandonedPercentage ?? 0,
                'percentAcessed' => $accessedPercentage ?? 0,
                'percentFinalized' => $finalizedPercentage ?? 0,
                'percentRecovered' => $recoveredPercentage ?? 0,
            ]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados - ReportsController - checkouts');
            report($e);

            return response()->json(null);
        }
    }

    public function getCheckoutsByOrigin(Request $request)
    {
        try {
            if (empty($request->project_id) || $request->project_id == null || $request->project_id == 'undefined') {
                return response()->json('projeto nao encontrado!');
            }

            $orders = Checkout::select(\DB::raw('count(*) as qtd_checkout, '.$request->origin.' as origin'));
            $affiliate = Affiliate::select('id')->where('user_id', auth()->user()->account_owner_id);

            if($request->project_id != "all"){
                $orders = $orders->where('project_id', hashids_decode($request->project_id));
                $affiliate = $affiliate->where('project_id', hashids_decode($request->project_id));
            }

            $affiliate = $affiliate->first();

            if (!empty($affiliate)) {
                $orders = $orders->where('affiliate_id', $affiliate->id);
            }

            $orders = $orders->whereBetween('created_at', [$request->start_date, date('Y-m-d', strtotime($request->end_date.' + 1 day'))])
                ->whereNotIn($request->origin, ['', 'null', null])
                ->groupBy($request->origin)
                ->orderBy('qtd_checkout', 'DESC')
                ->paginate(6);

            return CheckoutsByOriginResource::collection($orders);
        } catch (Exception $e) {
            report($e);
            return response()->json('Ocorreu algum erro');
        }
    }

    public function projections(Request $request)
    {
        try {
            $companyId = current(Hashids::decode($request->input('company')));

            $company = Company::where('user_id', auth()->user()->account_owner_id)
                ->where('id', $companyId)
                ->first();

            if (!empty($company->id)) {
                $transactionModel = new Transaction();

                $itens = $transactionModel->select([
                    DB::raw('(SUM(transactions.value) - (SUM(CASE WHEN transactions.status_enum = 12 THEN anticipated_transactions.value ELSE 0 END))) as value'),
                    DB::raw('DATE(transactions.release_date) as date'),
                ])
                    ->where('company_id', $companyId)
                    ->leftJoin('anticipated_transactions', 'transactions.id', 'anticipated_transactions.transaction_id')
                    ->whereIn('transactions.type', collect([2, 3, 4, 5]))
                    ->whereIn('transactions.status_enum', collect([
                        $transactionModel->present()
                            ->getStatusEnum('paid'),
                        $transactionModel->present()
                            ->getStatusEnum('anticipated'),
                    ]))
                    ->whereBetween('release_date', [
                        Carbon::now()->addDay()->format('Y-m-d'), Carbon::now()->addDays(30)
                            ->format('Y-m-d'),
                    ])
                    ->groupBy('date')
                    ->get();

                $transactions = [];

                $labelList = [];
                $dataFormated = Carbon::today()->addDay();
                $endDate = Carbon::today()->addDays(30);

                while ($dataFormated->lessThanOrEqualTo($endDate)) {
                    array_push($labelList, $dataFormated->format('d/m/Y'));
                    $dataFormated = $dataFormated->addDays(1);
                }

                $total = 0;

                foreach ($labelList as $label) {
                    $dateSearch = Carbon::createFromFormat('d/m/Y', $label)->format('Y-m-d');
                    $item = $itens->firstWhere('date', $dateSearch);
                    $transactions[] = [
                        'date' => $label,
                        'value' => (isset($item->value)) ? number_format(intval(preg_replace("/[^0-9]/", "",
                                $item->value)) / 100, 2, ',', '.') : '0,00',
                    ];
                    $total += $item->value ?? 0;
                }

                if ($total > 0) {
                    $transactions[] = [
                        'date' => 'Total',
                        'value' => number_format(intval(preg_replace("/[^0-9]/", "", $total)) / 100, 2, ',', '.')
                    ];
                }

                $transactionModel = new Transaction();

                $transactionTotal = $transactionModel->join('sales', 'transactions.sale_id', 'sales.id')
                    ->leftJoin('anticipated_transactions', 'transactions.id', 'anticipated_transactions.transaction_id')
                    ->select([
                        DB::raw('(SUM(transactions.value) - SUM(CASE WHEN transactions.status_enum = 12 THEN anticipated_transactions.value ELSE 0 END)) as value'),
                        DB::raw('(SUM(CASE WHEN sales.payment_method = 2 THEN transactions.value ELSE 0 END) -
                                                                            SUM(CASE WHEN transactions.status_enum = 12 AND sales.payment_method = 2 THEN anticipated_transactions.value ELSE 0 END)) AS valueBillet'),
                        DB::raw('(SUM(CASE WHEN sales.payment_method IN (1,3) THEN transactions.value ELSE 0 END) -
                                                                            SUM(CASE WHEN transactions.status_enum = 12 AND sales.payment_method IN(1,3) THEN anticipated_transactions.value ELSE 0 END)) AS valueCard'),
                    ])->where('company_id', $companyId)
                    ->whereIn('transactions.type', collect([2, 3, 4, 5, 8]))
                    ->whereIn('transactions.status_enum', collect([
                        $transactionModel->present()
                            ->getStatusEnum('paid'),
                        $transactionModel->present()
                            ->getStatusEnum('anticipated'),
                    ]))
                    ->whereBetween('release_date', [
                        Carbon::now()->addDay()->format('Y-m-d'), Carbon::now()
                            ->addDays(30)
                            ->format('Y-m-d'),
                    ])
                    ->groupBy('transactions.company_id')
                    ->first();

                $reportService = new ReportService();

                if ($company->country == 'usa') {
                    $currency = '$';
                } else {
                    $currency = 'R$';
                }

                $chartData = $reportService->getFinacialProjectionByDays($companyId, $currency);
            }

            if (empty($chartData)) {
                $chartData = [
                    'label_list' => ['', ''],
                    'transaction_data' => [0, 0],
                    'currency' => '',
                ];
            }

            return response()->json([
                'chartData' => $chartData,
                'transactions' => $transactions ?? 0,
                'totalValue' => isset($transactionTotal->value) ? number_format(intval(preg_replace("/[^0-9]/", "",
                        $transactionTotal->value)) / 100, 2, ',', '.') : '0,00',
                'valueBillet' => isset($transactionTotal->valueBillet) ? number_format(intval(preg_replace("/[^0-9]/",
                        "", $transactionTotal->valueBillet)) / 100, 2, ',', '.') : '0,00',
                'valueCard' => isset($transactionTotal->valueCard) ? number_format(intval(preg_replace("/[^0-9]/", "",
                        $transactionTotal->valueCard)) / 100, 2, ',', '.') : '0,00',
            ]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados - ReportsController - projections');
            report($e);

            return response()->json(null);
        }
    }

    public function projectionsExport(Request $request)
    {
        $data = $request->all();
        $checkData = collect();
        $header = ['Data', 'Valor'];
        $companyId = current(Hashids::decode($request->input('company')));

        $transactionModel = new Transaction();

        $itens = $transactionModel->select([
            DB::raw('(SUM(transactions.value) - (SUM(CASE WHEN transactions.status_enum = 12 THEN anticipated_transactions.value ELSE 0 END))) as value'),
            DB::raw('DATE(transactions.release_date) as date'),
        ])->join('companies', 'transactions.company_id', 'companies.id')
            ->leftJoin('anticipated_transactions', 'transactions.id', 'anticipated_transactions.transaction_id')
            ->where('companies.user_id', auth()->user()->id)
            ->where('companies.id', $companyId)
            ->whereIn('transactions.type', collect([2, 3, 4, 5]))
            ->whereIn('transactions.status_enum', collect([
                $transactionModel->present()
                    ->getStatusEnum('paid'),
                $transactionModel->present()
                    ->getStatusEnum('anticipated'),
            ]))
            ->whereBetween('release_date', [
                Carbon::now()->addDay()->format('Y-m-d'), Carbon::now()->addDays(30)
                    ->format('Y-m-d'),
            ])
            ->groupBy('date')
            ->get();

        $labelList = [];
        $dataFormated = Carbon::today()->addDay();
        $endDate = Carbon::today()->addDays(30);

        while ($dataFormated->lessThanOrEqualTo($endDate)) {
            array_push($labelList, $dataFormated->format('d/m/Y'));
            $dataFormated = $dataFormated->addDays(1);
        }

        $dataArray = [];
        $total = 0;

        foreach ($labelList as $label) {
            $dateSearch = Carbon::createFromFormat('d/m/Y', $label)->format('Y-m-d');
            $item = $itens->firstWhere('date', $dateSearch);
            $dataArray = [
                'date' => $label,
                'value' => (isset($item->value)) ? number_format(intval(preg_replace("/[^0-9]/", "",
                        $item->value)) / 100, 2, ',', '.') : '0,00',
            ];
            $checkData->push(collect($dataArray));
            $total += $item->value ?? 0;
        }

        if ($total > 0) {
            $checkData->push(collect([
                'date' => 'Total',
                'value' => number_format(intval(preg_replace("/[^0-9]/", "", $total)) / 100, 2, ',', '.')
            ]));
        }

        return Excel::download(new ReportExport($checkData, $header, 11), 'cloudfox-transacoes.'.$data['format']);
    }

    public function coupons(Request $request)
    {
        try {
            $projectId = $request->input('project');
            $projects = UserProject::where('user_id', auth()->user()->account_owner_id)
                ->with('project')
                ->where('type', 'producer');

            if (!empty($projectId)) {
                $projects = $projects->where('project_id', Hashids::decode($projectId));
            }
            $projects = $projects->get();

            if (empty($request->input('status'))) {
                $status = [1, 2, 4, 6, 7, 8, 12, 20, 22];
            } else {
                $status = $request->input("status") == 7 ? [7, 22] : [$request->input("status")];
            }

            $dateRange = FoxUtils::validateDateRange($request->input("date_range"));

            $coupons = Sale::select([
                DB::raw('COUNT(id) as amount'),
                'project_id',
                'cupom_code'
            ])->whereIn('project_id', $projects->pluck('project_id'))
                ->whereIn('status', $status)
                ->where('cupom_code','!=', '')
                ->whereBetween('start_date', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59'])
                ->groupBy('cupom_code', 'project_id')
                ->orderByRaw('amount DESC')
                ->paginate(10);

            foreach ($coupons as $key => $coupon) {
                $coupons[$key]->project_name = $projects->firstWhere('project_id',
                        $coupon->project_id)->project->name ?? '';
            }

            return ReportCouponResource::collection($coupons);
        } catch (Exception $e) {
            report($e);
            return response()->json(null);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function pendingBalance(Request $request)
    {
        try {
            activity()->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela saldo pendente');

            $saleService = new SaleService();

            $data = $request->all();

            $sales = $saleService->getPendingBalance($data);

            return PendingBalanceResource::collection($sales);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar saldo pendente ReportsApiController - pendingBalance');
            report($e);

            return response()->json(['message' => 'Erro ao carregar vendas'], 400);
        }
    }

    public function resumePendingBalance (Request $request)
    {
        try {
            $saleService = new SaleService();

            $data = $request->all();

            $resume = $saleService->getResumePending($data);

            return response()->json($resume);
        } catch (Exception $e) {
            Log::warning('Erro ao exibir resumo dos saldos pendete ReportsApiController - resumePendingBalance');
            report($e);

            return response()->json(['error' => 'Erro ao exibir resumo das vendas'], 400);
        }
    }

    public function blockedBalance(Request $request)
    {
        try {
            activity()->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela saldo bloqueado');

            $saleService = new SaleService();

            $data = $request->all();

            $sales = $saleService->getPaginetedBlocked($data);

            return TransactionBlockedResource::collection($sales);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar vendas bloqueadas ReportsApiController - blockedBalance');
            report($e);

            return response()->json(['message' => 'Erro ao carregar vendas'], 400);
        }
    }

    public function resumeBlockedBalance(Request $request)
    {
        try {
            $saleService = new SaleService();

            $data = $request->all();

            $resume = $saleService->getResumeBlocked($data);

            return response()->json($resume);
        } catch (Exception $e) {
            Log::warning('Erro ao exibir resumo das venda bloqueadas ReportsApiController - resumeBlockedBalance');
            report($e);

            return response()->json(['error' => 'Erro ao exibir resumo das vendas'], 400);
        }
    }
}
