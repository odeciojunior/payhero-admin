<?php

namespace Modules\Reports\Http\Controllers;

use App\Entities\Plan;
use DateTime;
use Exception;
use Carbon\Carbon;
use App\Entities\Sale;
use App\Entities\Company;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JMS\Serializer\Tests\Fixtures\Discriminator\Car;
use Matrix\Builder;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Reports\Transformers\SalesByOriginResource;

class ReportsController extends Controller
{
    /**
     * @var UserProject
     */
    private $projectsModel;
    /**
     * @var Project
     */
    private $userProjectModel;
    /**
     * @var Sale
     */
    private $salesModel;
    /**
     * @var Company
     */
    private $companyModel;
    /**
     * @var Plan
     */
    private $planModel;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getUserProjects()
    {
        if (!$this->userProjectModel) {

            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getProjects()
    {
        if (!$this->projectsModel) {
            $this->projectsModel = app(Project::class);
        }

        return $this->projectsModel;
    }

    /**
     * @return Sale|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getSales()
    {
        if (!$this->salesModel) {
            $this->salesModel = app(Sale::class);
        }

        return $this->salesModel;
    }

    /**
     * @return Company|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getCompany()
    {
        if (!$this->companyModel) {
            $this->companyModel = app(Company::class);
        }

        return $this->companyModel;
    }

    public function getPlan()
    {
        if (!$this->planModel) {
            $this->planModel = app(Plan::class);
        }

        return $this->planModel;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $user         = auth()->user();
            $userProjects = $this->getUserProjects()->with(['projectId'])->where('user', $user->id)->get();

            if (isset($userProjects) && $userProjects->count() > 0) {
                $projects = [];
                foreach ($userProjects as $userProject) {
                    if (isset($userProject->projectId)) {
                        $projects [] = $userProject->projectId;
                    }
                }

                if (!empty($projects) && count($projects) > 0) {
                    return view('reports::index', compact('projects', 'userProjects'));
                }
            }

            return view('reports::index', compact('userProjects'));
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados - ReportsController - index');
            report($e);

            return redirect()->back();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getValues(Request $request)
    {
        try {
            $dataSearch       = $request->all();
            $projectId        = current(Hashids::decode($request->input('project')));
            $requestStartDate = $request->input('startDate');
            $requestEndDate   = $request->input('endDate');
            if ($projectId) {
                $userProject = $this->getUserProjects()->where([
                                                                   ['user', auth()->user()->id],
                                                                   ['type', 'producer'],
                                                                   ['project', $projectId],
                                                               ])->first();

                if ($userProject) {
                    $sales = $this->getSales()
                                  ->select('sales.*', 'transaction.value', 'checkout.is_mobile')
                                  ->leftJoin('transactions as transaction', function($join) use ($userProject) {
                                      $join->where('transaction.company', $userProject->company);
                                      $join->whereIn('transaction.status', ['paid', 'transfered']);
                                      $join->on('transaction.sale', '=', 'sales.id');
                                  })
                                  ->leftJoin('checkouts as checkout', function($join) {
                                      $join->on('sales.checkout', 'checkout.id');
                                  })
                                  ->where([['sales.project', $projectId], ['sales.owner', auth()->user()->id]]);

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

                    // itens
                    $itens = $this->getSales()
                                  ->select(\DB::raw('count(*) as count'), 'plan_sale.plan')
                                  ->leftJoin('plans_sales as plan_sale', function($join) {
                                      $join->on('plan_sale.sale', '=', 'sales.id');
                                  })
                                  ->where('sales.status', 1)->where('project', $projectId);

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

                    $itens = $itens->groupBy('plan_sale.plan')->orderBy('count', 'desc')->limit(3)->get()->toArray();
                    $plans = [];
                    foreach ($itens as $key => $iten) {
                        $plan                      = $this->getPlan()->with('products')->find($iten['plan']);
                        $plans[$key]['name']       = $plan->name . ' - ' . $plan->description;
                        $plans[$key]['photo']      = $plan->products[0]->photo;
                        $plans[$key]['quantidade'] = $iten['count'];
                        unset($plan);
                    }

                    // calculos dashboard
                    $salesDetails = $this->getSales()->select([

                                                                  DB::raw('SUM(CASE WHEN sales.status = 1 THEN 1 ELSE 0 END) AS contSalesAproved'),
                                                                  DB::raw('SUM(CASE WHEN sales.status = 2 THEN 1 ELSE 0 END) AS contSalesPending'),
                                                                  DB::raw('SUM(CASE WHEN sales.status = 3 THEN 1 ELSE 0 END) AS contSalesRecused'),
                                                                  DB::raw('SUM(CASE WHEN sales.status = 4 THEN 1 ELSE 0 END) AS contSalesChargeBack'),
                                                                  DB::raw('SUM(CASE WHEN sales.status = 5 THEN 1 ELSE 0 END) AS contSalesCanceled'),
                                                              ])
                                         ->where('owner', auth()->user()->id)
                                         ->where('project', $projectId);
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

                    if ($userProject->companyId->country == 'usa') {
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

                    $chartData     = $this->getChartData($dataSearch, $projectId, $currency);
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

                        $ticketMedio = number_format(intval(preg_replace("/[^0-9]/", "", $totalPaidValueAproved) / $countSalesAproved) / 100, 2, ',', '.');
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

            return redirect()->back();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getSalesByOrigin(Request $request)
    {
        $userCompanies = $this->getCompany()->where('user_id', auth()->user()->id)->pluck('id')->toArray();
        if (!empty($request->project_id) && $request->project_id != null && $request->project_id != 'undefined') {
            $orders = $this->getSales()
                           ->select(\DB::raw('count(*) as sales_amount, SUM(transaction.value) as value, checkout.' . $request->origin . ' as origin'))
                           ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                               $join->on('transaction.sale', '=', 'sales.id');
                               $join->whereIn('transaction.company', $userCompanies);
                           })
                           ->leftJoin('checkouts as checkout', function($join) {
                               $join->on('checkout.id', '=', 'sales.checkout');
                           })
                           ->where('sales.project', current(Hashids::decode($request->project_id)))
                           ->where('sales.status', 1)
                           ->whereBetween('start_date', [$request->start_date, date('Y-m-d', strtotime($request->end_date . ' + 1 day'))])
                           ->whereNotIn('checkout.' . $request->origin, ['', 'null'])
                           ->whereNotNull('checkout.' . $request->origin)
                           ->groupBy('checkout.' . $request->origin)
                           ->orderBy('sales_amount', 'DESC');

            return SalesByOriginResource::collection($orders->paginate(6));
        }

        return 0;
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @return array|null
     */
    private function getChartData($date, $projectId, $currency)
    {
        if ($date['startDate'] == $date['endDate']) {
            return $this->getByHours($date, $projectId, $currency);
        } else if ($date['startDate'] != $date['endDate']) {
            $data       = null;
            $startDate  = Carbon::createFromFormat('Y-m-d', $date['startDate'], 'America/Sao_Paulo');
            $endDate    = Carbon::createFromFormat('Y-m-d', $date['endDate'], 'America/Sao_Paulo');
            $diffInDays = $endDate->diffInDays($startDate);
            if ($projectId) {
                if ($diffInDays <= 20) {
                    return $this->getByDays($date, $projectId, $currency);
                } else if ($diffInDays > 20 && $diffInDays <= 40) {
                    return $this->getByTwentyDays($date, $projectId, $currency);
                } else if ($diffInDays > 40 && $diffInDays <= 60) {
                    return $this->getByFortyDays($date, $projectId, $currency);
                } else if ($diffInDays > 60 && $diffInDays <= 140) {
                    return $this->getByWeek($date, $projectId, $currency);
                } else if ($diffInDays > 140) {
                    return $this->getByMonth($date, $projectId, $currency);
                }
            } else {

                return [
                    'label_list'       => ['', ''],
                    'credit_card_data' => [0, 0],
                    'boleto_data'      => [0, 0],
                    'currency'         => $currency,
                ];
            }
        }
    }

    /**
     * @param $data
     * @param $projectId
     * @param $currency
     * @return array
     */
    private function getByHours($data, $projectId, $currency)
    {
        date_default_timezone_set('America/Sao_Paulo');

        if (Carbon::parse($data['startDate'])->format('m/d/y') == Carbon::now()->format('m/d/y')) {

            $labelList   = [];
            $currentHour = date('H');
            $startHour   = 0;
            while ($startHour <= $currentHour) {
                array_push($labelList, $startHour . 'h');
                $startHour++;
            }
        } else {
            $labelList = [
                '0h', '1h', '2h', '3h', '4h', '5h', '6h', '7h', '8h', '9h', '10h', '11h',
                '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h', '21h', '22h', '23h',
            ];
        }

        $userCompanies = $this->getCompany()
                              ->where('user_id', \Auth::user()->id)
                              ->pluck('id')
                              ->toArray();

        $orders = $this->getSales()
                       ->select(\DB::raw('count(*) as count, HOUR(sales.start_date) as hour, SUM(transaction.value) as value, sales.payment_method'))
                       ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                           $join->on('transaction.sale', '=', 'sales.id');
                           $join->whereIn('transaction.company', $userCompanies);
                       })
                       ->where('sales.owner', \Auth::user()->id)
                       ->where('sales.project', $projectId)
            //                       ->where('sales.status', 1)
                       ->whereDate('sales.start_date', $data['startDate'])
                       ->groupBy('hour', 'sales.payment_method')
                       ->get()->toArray();

        $creditCardData = [];
        $boletoData     = [];

        foreach ($labelList as $label) {
            $creditCardValue = 0;
            $boletoValue     = 0;
            foreach ($orders as $order) {
                if ($order['hour'] == preg_replace("/[^0-9]/", "", $label)) {
                    if ($order['payment_method'] == 1) {
                        $creditCardValue = substr(intval($order['value']), 0, -2);
                    } else {
                        $boletoValue = substr(intval($order['value']), 0, -2);
                    }
                }
            }
            array_push($creditCardData, $creditCardValue);
            array_push($boletoData, $boletoValue);
        }

        return [
            'label_list'       => $labelList,
            'credit_card_data' => $creditCardData,
            'boleto_data'      => $boletoData,
            'currency'         => $currency,
        ];
    }

    /**
     * @param $data
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByDays($data, $projectId, $currency)
    {
        try {

            $labelList    = [];
            $dataFormated = Carbon::parse($data['startDate']);
            $endDate      = Carbon::parse($data['endDate']);
            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d-m'));
                $dataFormated = $dataFormated->addDays(1);
            }
            $data['endDate'] = date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'));

            $userCompanies = $this->getCompany()->where('user_id', auth()->user()->id)->pluck('id')->toArray();

            $orders = $this->getSales()
                           ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                           ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                               $join->on('transaction.sale', '=', 'sales.id');
                               $join->whereIn('transaction.company', $userCompanies);
                           })
                           ->where('sales.owner', auth()->user()->id)
                           ->where('sales.project', $projectId)
                //                           ->where('sales.status', 1)
                           ->whereBetween('start_date', [$data['startDate'], date('Y-m-d', strtotime($data['endDate'] . ' + 1 day'))])
                           ->groupBy('date', 'sales.payment_method')
                           ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];

            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                foreach ($orders as $order) {
                    if (Carbon::parse($order['date'])->format('d-m') == $label) {
                        if ($order['payment_method'] == 1) {
                            $creditCardValue = substr(intval($order['value']), 0, -2);
                        } else {
                            $boletoValue = substr(intval($order['value']), 0, -2);
                        }
                    }
                }
                array_push($creditCardData, $creditCardValue);
                array_push($boletoData, $boletoValue);
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByTwentyDays($date, $projectId, $currency)
    {
        try {
            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(1);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(2);
                if ($dataFormated->diffInDays($endDate) < 2 && $dataFormated->diffInDays($endDate) > 0) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $this->getCompany()->where('user_id', auth()->user()->id)->pluck('id')->toArray();

            $orders = $this->getSales()
                           ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                           ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                               $join->on('transaction.sale', '=', 'sales.id');
                               $join->whereIn('transaction.company', $userCompanies);
                           })
                           ->where('sales.owner', auth()->user()->id)
                           ->where('sales.project', $projectId)
                           ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                           ->groupBy('date', 'sales.payment_method')
                           ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                foreach ($orders as $order) {
                    if ((Carbon::parse($order['date'])
                               ->subDays(1)->format('d/m') == $label) || (Carbon::parse($order['date'])
                                                                                ->format('d/m') == $label)) {

                        if ($order['payment_method'] == 1) {
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } else {
                            $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        }
                    }
                }
                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByFortyDays($date, $projectId, $currency)
    {
        try {
            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(2);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(3);
                if ($dataFormated->diffInDays($endDate) < 3) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }

            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . '+ 1 day'));
            $userCompanies   = $this->getCompany()->where('user_id', auth()->user()->id)->pluck('id')->toArray();

            $orders = $this->getSales()
                           ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                           ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                               $join->on('transaction.sale', '=', 'sales.id');
                               $join->whereIn('transaction.company', $userCompanies);
                           })
                           ->where('sales.owner', auth()->user()->id)
                           ->where('sales.project', $projectId)
                           ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                           ->groupBy('date', 'sales.payment_method')
                           ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;

                foreach ($orders as $order) {
                    for ($x = 1; $x <= 3; $x++) {
                        if ((Carbon::parse($order['date'])->addDays($x)->format('d/m') == $label)) {

                            if ($order['payment_method'] == '1') {
                                $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            } else {
                                $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            }
                        }
                    }
                }

                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados ReportsController - getByFortyDays');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByWeek($date, $projectId, $currency)
    {
        try {
            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate'])->addDays(6);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->addDays(7);
                if ($dataFormated->diffInDays($endDate) < 7) {
                    array_push($labelList, $dataFormated->format('d/m'));
                    $dataFormated = $dataFormated->addDays($dataFormated->diffInDays($endDate));
                    array_push($labelList, $dataFormated->format('d/m'));
                    break;
                }
            }
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $this->getCompany()->where('user_id', auth()->user()->id)->pluck('id')->toArray();

            $orders = $this->getSales()
                           ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                           ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                               $join->on('transaction.sale', '=', 'sales.id');
                               $join->whereIn('transaction.company', $userCompanies);
                           })
                           ->where('sales.owner', auth()->user()->id)
                           ->where('sales.project', $projectId)
                           ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                           ->groupBy('date', 'sales.payment_method')
                           ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                foreach ($orders as $order) {
                    for ($x = 1; $x <= 6; $x++) {
                        if ((Carbon::parse($order['date'])->addDays($x)->format('d/m') == $label)) {

                            if ($order['payment_method'] == 1) {
                                $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            } else {
                                $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            }
                        }
                    }
                }
                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }

    /**
     * @param $date
     * @param $projectId
     * @param $currency
     * @param $diffInDays
     * @return array
     */
    private function getByMonth($date, $projectId, $currency)
    {
        try {
            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate']);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->lessThanOrEqualTo($endDate)) {
                array_push($labelList, $dataFormated->format('m/y'));
                $dataFormated = $dataFormated->addMonths(1);
            }
            $date['endDate'] = date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'));

            $userCompanies = $this->getCompany()->where('user_id', auth()->user()->id)->pluck('id')->toArray();

            $orders = $this->getSales()
                           ->select(\DB::raw('count(*) as count, DATE(sales.start_date) as date, SUM(transaction.value) as value, sales.payment_method'))
                           ->leftJoin('transactions as transaction', function($join) use ($userCompanies) {
                               $join->on('transaction.sale', '=', 'sales.id');
                               $join->whereIn('transaction.company', $userCompanies);
                           })
                           ->where('sales.owner', auth()->user()->id)
                           ->where('sales.project', $projectId)
                           ->whereBetween('start_date', [$date['startDate'], date('Y-m-d', strtotime($date['endDate'] . ' + 1 day'))])
                           ->groupBy('date', 'sales.payment_method')
                           ->get()->toArray();

            $creditCardData = [];
            $boletoData     = [];
            foreach ($labelList as $label) {
                $creditCardValue = 0;
                $boletoValue     = 0;
                foreach ($orders as $order) {
                    if (Carbon::parse($order['date'])->format('m/y') == $label) {

                        if ($order['payment_method'] == 1) {
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } else {
                            $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        }
                    }
                }
                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            return [
                'label_list'       => $labelList,
                'credit_card_data' => $creditCardData,
                'boleto_data'      => $boletoData,
                'currency'         => $currency,
            ];
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados');
            report($e);
        }
    }
}
