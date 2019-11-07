<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Sale;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ReportService;
use Modules\Reports\Transformers\SalesByOriginResource;

class ReportsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(request $request)
    {

        try {
            $userProjectModel = new UserProject();
            $salesModel       = new Sale();
            $planModel        = new Plan();

            $dataSearch = $request->all();
            $projectId  = current(Hashids::decode($request->input('project')));

            $requestStartDate = $request->input('startDate');
            $requestEndDate   = $request->input('endDate');
            if ($projectId) {
                $userProject = $userProjectModel->where([
                                                            ['user_id', auth()->user()->account_owner_id],
                                                            ['type', 'producer'],
                                                            ['project_id', $projectId],
                                                        ])->first();

                $companies = Company::where('user_id', auth()->user()->account_owner_id)->pluck('id');

                if ($userProject) {
                    $sales = $salesModel
                        ->select('sales.*', 'transaction.value', 'checkout.is_mobile')
                        ->leftJoin('transactions as transaction', function($join) use ($companies) {
                            $join->whereIn('transaction.company_id', $companies);
                            $join->whereIn('transaction.status', ['paid', 'transfered', 'anticipated']);
                            $join->on('transaction.sale_id', '=', 'sales.id');
                        })
                        ->leftJoin('checkouts as checkout', function($join) {
                            $join->on('sales.checkout_id', 'checkout.id');
                        })
                        ->where([['sales.project_id', $projectId], ['sales.owner_id', auth()->user()->account_owner_id]]);

                    if (!empty($requestStartDate) && !empty($requestEndDate)) {
                        $sales->whereBetween('sales.start_date', [$requestStartDate, date('Y-m-d', strtotime($requestEndDate . ' + 1 day'))]);
                    } else {
                        if (!empty($requestStartDate)) {
                            $sales->whereDate('sales.start_date', '>=', $requestStartDate);
                        }

                        if (!empty($requestEndDate)) {
                            $sales->whereDate('sales.end_date', '<', date('Y-m-d', strtotime($requestEndDate . ' + 1 day')));
                        }
                    }
                    $sales     = $sales->get();
                    $contSales = $sales->count();

                    $itens = $salesModel
                        ->select(\DB::raw('count(*) as count'), 'plan_sale.plan_id')
                        ->leftJoin('plans_sales as plan_sale', function($join) {
                            $join->on('plan_sale.sale_id', '=', 'sales.id');
                        })
                        ->where('sales.status', 1)->where('project_id', $projectId);

                    if (!empty($requestStartDate) && !empty($requestEndDate)) {
                        $itens->whereBetween('sales.start_date', [$requestStartDate, date('Y-m-d', strtotime($requestEndDate . ' + 1 day'))]);
                    } else {
                        if (!empty($requestStartDate)) {
                            $itens->whereDate('sales.start_date', '>=', $requestStartDate);
                        }

                        if (!empty($requestEndDate)) {
                            $itens->whereDate('sales.end_date', '<', date('Y-m-d', strtotime($requestEndDate . ' + 1 day')));
                        }
                    }

                    $itens = $itens->groupBy('plan_sale.plan_id')->orderBy('count', 'desc')->limit(3)->get()->toArray();
                    $plans = [];
                    foreach ($itens as $key => $iten) {
                        $plan                      = $planModel->with('products')->find($iten['plan_id']);
                        $plans[$key]['name']       = $plan->name . ' - ' . $plan->description;
                        $plans[$key]['photo']      = $plan->products[0]->photo;
                        $plans[$key]['quantidade'] = $iten['count'];
                        unset($plan);
                    }

                    // calculos dashboard
                    $salesDetails = $salesModel->select([
                                                            DB::raw('SUM(CASE WHEN sales.status = 1 THEN 1 ELSE 0 END) AS contSalesAproved'),
                                                            DB::raw('SUM(CASE WHEN sales.status = 2 THEN 1 ELSE 0 END) AS contSalesPending'),
                                                            DB::raw('SUM(CASE WHEN sales.status = 3 THEN 1 ELSE 0 END) AS contSalesRecused'),
                                                            DB::raw('SUM(CASE WHEN sales.status = 4 THEN 1 ELSE 0 END) AS contSalesChargeBack'),
                                                            DB::raw('SUM(CASE WHEN sales.status = 5 THEN 1 ELSE 0 END) AS contSalesCanceled'),
                                                        ])
                                               ->where('owner_id', auth()->user()->account_owner_id)
                                               ->where('project_id', $projectId);
                    if ($requestStartDate != '' && $requestEndDate != '') {
                        $salesDetails->whereBetween('start_date', [$requestStartDate, date('Y-m-d', strtotime($requestEndDate . ' + 1 day'))]);
                    } else {
                        if (!empty($requestStartDate)) {
                            $salesDetails->whereDate('start_date', '>=', $requestStartDate);
                        }

                        if (!empty($requestEndDate)) {
                            $salesDetails->whereDate('end_date', '<', date('Y-m-d', strtotime($requestEndDate . ' + 1 day')));
                        }
                    }
                    $details               = $salesDetails->get();
                    $countSalesAproved     = $details[0]->contSalesAproved;
                    $countSalesPending     = $details[0]->contSalesPending;
                    $countSalesRecused     = $details[0]->contSalesRecused;
                    $countSalesChargeBack  = $details[0]->contSalesChargeBack;
                    $countSalesCanceled    = $details[0]->contSalesCanceled;
                    $totalValueCreditCard  = 0;
                    $contCreditCardAproved = 0;
                    $totalValueBoleto      = 0;
                    $contBoletoAproved     = 0;
                    $contCreditCard        = 0;
                    $contBoleto            = 0;
                    $totalPaidValueAproved = 0;
                    $contMobile            = 0;
                    $contDesktop           = 0;
                    $ticketMedio           = 0;

                    if ($userProject->company->country == 'usa') {
                        $currency = '$';
                    } else {
                        $currency = 'R$';
                    }

                    if (count($sales) > 0) {
                        foreach ($sales as $sale) {
                            if ($sale->payment_method == 1 && $sale->status == 1 && $sale->value != null) {
                                $totalValueCreditCard += $sale->value;
                                $contCreditCardAproved++;
                            }
                            if ($sale->payment_method == 2 && $sale->status == 1 && $sale->value != null) {
                                $totalValueBoleto += $sale->value;
                                $contBoletoAproved++;
                            }

                            // cartao
                            if ($sale->payment_method == 1) {
                                $contCreditCard++;
                            }
                            // boleto
                            if ($sale->payment_method == 2) {
                                $contBoleto++;
                            }
                            // vendas aprovadas
                            if ($sale->status == 1) {
                                $totalPaidValueAproved += $sale->value;
                            }

                            if ($sale->is_mobile) {
                                $contMobile++;
                            } else {
                                $contDesktop++;
                            }
                        }
                    }

                    $reportService = new ReportService();

                    $chartData = $reportService->getChartData($dataSearch, $projectId, $currency);

                    $cartaoConvert = $contCreditCardAproved . '/' . $contCreditCard;
                    $boletoConvert = $contBoletoAproved . '/' . $contBoleto;

                    if ($contBoleto != 0) {
                        $convercaoBoleto = number_format((intval($contBoletoAproved) * 100) / intval($contBoleto), 2, ',', ' . ');
                    }

                    if ($contCreditCard != 0) {
                        $convercaoCreditCard = number_format((intval($contCreditCardAproved) * 100) / intval($contCreditCard), 2, ',', ' . ');
                    }

                    if ($contSales > 0) {
                        $conversaoMobile  = number_format((intval($contMobile) * 100) / intval($contSales), 2, ',', ' . ');
                        $conversaoDesktop = number_format((intval($contDesktop) * 100) / intval($contSales), 2, ',', ' . ');
                    } else {
                        $conversaoMobile  = "0.00";
                        $conversaoDesktop = "0.00";
                    }

                    if ($totalPaidValueAproved != 0) {
                        $totalPercentPaidCredit = number_format((intval($totalValueCreditCard) * 100) / intval($totalPaidValueAproved), 2, ',', ' . ');
                        $totalPercentPaidBoleto = number_format((intval($totalValueBoleto) * 100) / intval($totalPaidValueAproved), 2, ',', ' . ');
                        $ticketMedio            = number_format(intval(preg_replace("/[^0-9]/", "", $totalPaidValueAproved) / $countSalesAproved) / 100, 2, ',', '.');
                    }
                }
            }
            if (empty($chartData)) {
                $chartData = [
                    'label_list'       => ['', ''],
                    'credit_card_data' => [0, 0],
                    'boleto_data'      => [0, 0],
                    'currency'         => '',
                ];
            }

            return response()->json([
                                        'totalPaidValueAproved'  => isset($totalPaidValueAproved) ? number_format(intval(preg_replace("/[^0-9]/", "", $totalPaidValueAproved)) / 100, 2, ',', '.') : 00,
                                        'contAproved'            => $countSalesAproved ?? 0,
                                        'contBoleto'             => $contBoleto ?? 0,
                                        'contRecused'            => $countSalesRecused ?? 0,
                                        'contChargeBack'         => $countSalesChargeBack ?? 0,
                                        'contPending'            => $countSalesPending ?? 0,
                                        'contCanceled'           => $countSalesCanceled ?? 0,
                                        'totalPercentCartao'     => $totalPercentPaidCredit ?? 0,
                                        'totalPercentPaidBoleto' => $totalPercentPaidBoleto ?? 0,
                                        'totalValueBoleto'       => isset($totalValueBoleto) ? number_format(intval(preg_replace("/[^0-9]/", "", $totalValueBoleto)) / 100, 2, ',', '.') : 00,
                                        'totalValueCreditCard'   => isset($totalValueCreditCard) ? number_format(intval(preg_replace("/[^0-9]/", "", $totalValueCreditCard)) / 100, 2, ',', '.') : 00,
                                        'chartData'              => $chartData,
                                        'currency'               => $currency ?? 0,
                                        'convercaoBoleto'        => $convercaoBoleto ?? 0,
                                        'convercaoCreditCard'    => $convercaoCreditCard ?? 0,
                                        'conversaoMobile'        => $conversaoMobile ?? 0,
                                        'conversaoDesktop'       => $conversaoDesktop ?? 0,
                                        'cartaoConvert'          => $cartaoConvert ?? 0,
                                        'boletoConvert'          => $boletoConvert ?? 0,
                                        'plans'                  => $plans ?? 0,
                                        'ticketMedio'            => isset($ticketMedio) ? number_format(intval(preg_replace("/[^0-9]/", "", $ticketMedio)) / 100, 2, ',', '.') : 0,
                                    ]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados - ReportsController - index');
            report($e);

            return response()->json(null);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getSalesByOrigin(Request $request)
    {
        try {
            $companyModel = new Company();
            $saleModel    = new Sale();

            $userCompanies = $companyModel->where('user_id', auth()->user()->account_owner_id)->pluck('id')->toArray();

            if (!empty($request->project_id) && $request->project_id != null && $request->project_id != 'undefined') {

                $orders = $saleModel
                    ->select(\DB::raw('count(*) as sales_amount, SUM(transaction.value) as value, checkout.' . $request->origin . ' as origin'))
                    ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                        $join->on('transaction.sale_id', '=', 'sales.id');
                        $join->whereIn('transaction.company_id', $userCompanies);
                    })
                    ->leftJoin('checkouts as checkout', function($join) {
                        $join->on('checkout.id', '=', 'sales.checkout_id');
                    })
                    ->where('sales.project_id', current(Hashids::decode($request->project_id)))
                    ->where('sales.status', 1)
                    ->whereBetween('start_date', [$request->start_date, date('Y-m-d', strtotime($request->end_date . ' + 1 day'))])
                    ->whereNotIn('checkout.' . $request->origin, ['', 'null'])
                    ->whereNotNull('checkout.' . $request->origin)
                    ->groupBy('checkout.' . $request->origin)
                    ->orderBy('sales_amount', 'DESC');

                return SalesByOriginResource::collection($orders->paginate(6));
            }

            return response()->json('project not found');
        } catch (Exception $e) {
            Log::warning('erro na tabela de origens');

            return response()->json('Ocorreu algum erro');
        }
    }
}
