<?php

namespace Modules\Reports\Http\Controllers;

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
use Vinkla\Hashids\Facades\Hashids;

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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        try {
            $user         = auth()->user();
            $userProjects = $this->getUserProjects()->with(['projectId'])->where('user', $user->id)->get();

            $projects = [];
            foreach ($userProjects as $userProject) {
                if (isset($userProject->projectId)) {
                    $projects [] = $userProject->projectId;
                }
            }

            return view('reports::index', compact('projects', 'userProjects'));
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
            $dataSearch = $request->all();

            $projectId = current(Hashids::decode($dataSearch['project']));

            $userProject = $this->getUserProjects()->where([
                                                               ['user', auth()->user()->id],
                                                               ['type', 'producer'],
                                                               ['project', $projectId],
                                                           ])->first();

            if (isset($dataSearch['project'])) {
                $sales = $this->getSales()
                              ->select('sales.*', 'transaction.value')
                              ->leftJoin('transactions as transaction', function($join) use ($userProject) {
                                  $join->where('transaction.company', '=', $userProject->company);
                                  $join->where('transaction.status', 'paid');
                                  $join->on('transaction.sale', '=', 'sales.id');
                              })
                              ->where([['project', $projectId], ['owner', auth()->user()->id]]);

                if ($dataSearch['startDate'] != '' && $dataSearch['endDate'] != '') {
                    $sales->whereBetween('start_date', [$dataSearch['startDate'], date('Y-m-d', strtotime($dataSearch['endDate'] . ' + 1 day'))]);
                } else {
                    if ($request->$dataSearch['startDate'] != '') {
                        $sales->whereDate('start_date', '>=', $dataSearch['startDate']);
                    }

                    if ($request->data_final != '') {
                        $sales->whereDate('end_date', '<', date('Y-m-d', strtotime($dataSearch['endDate'] . ' + 1 day')));
                    }
                }

                $sales = $sales->get();

                $contBoleto     = 0;
                $contRecused    = 0;
                $contAproved    = 0;
                $contChargeBack = 0;

                $totalPercentPaidCredit = 0;
                $totalPercentPaidBoleto = 0;

                $totalPaidValueAproved = '000';

                $totalValueBoleto     = '000';
                $totalValueCreditCard = '000';

                if (count($sales) > 0) {
                    foreach ($sales as $sale) {

                        // cartao
                        if ($sale->payment_method == 1 && $sale->status == 1) {
                            $totalValueCreditCard += $sale->value;
                        }
                        if ($sale->payment_method == 2 && $sale->status == 1) {
                            $totalValueBoleto += $sale->value;
                        }
                        // boleto
                        if ($sale->payment_method == 2) {
                            $contBoleto++;
                        }

                        // vendas aprovadas
                        if ($sale->status == 1) {
                            $totalPaidValueAproved += $sale->value;
                            $contAproved++;
                        }

                        // vendas recusadas
                        if ($sale->status == 3) {
                            $contRecused++;
                        }

                        // vendas chargeback
                        if ($sale->status == 4) {
                            $contChargeBack++;
                        }
                    }

                    if ($totalPaidValueAproved != 0) {
                        $totalPercentPaidCredit = number_format((intval($totalValueCreditCard) * 100) / intval($totalPaidValueAproved), 2, ',', ' . ');
                        $totalPercentPaidBoleto = number_format((intval($totalValueBoleto) * 100) / intval($totalPaidValueAproved), 2, ',', ' . ');
                    }

                    //$totalValueBoleto = number_format(intval($totalValueBoleto), 2, ',', ' . ');
                    //$totalValueCreditCard = number_format(intval($totalValueCreditCard), 2, ',', '.');

                }
            }

            if ($userProject->companyId->country == 'usa') {
                $currency = '$';
            } else {
                $currency = 'R$';
            }

            $chartData = $this->getChartData($dataSearch, $projectId, $currency);

            return response()->json([
                                        'totalPaidValueAproved'  => number_format(intval(preg_replace("/[^0-9]/", "", $totalPaidValueAproved)) / 100, 2, ',', '.'),
                                        'contAproved'            => $contAproved,
                                        'contBoleto'             => $contBoleto,
                                        'contRecused'            => $contRecused,
                                        'contChargeBack'         => $contChargeBack,
                                        'totalPercentCartao'     => $totalPercentPaidCredit,
                                        'totalPercentPaidBoleto' => $totalPercentPaidBoleto,
                                        'totalValueBoleto'       => number_format(intval(preg_replace("/[^0-9]/", "", $totalValueBoleto)) / 100, 2, ',', '.'),
                                        'totalValueCreditCard'   => number_format(intval(preg_replace("/[^0-9]/", "", $totalValueCreditCard)) / 100, 2, ',', '.'),
                                        'chartData'              => $chartData,
                                        'currency'               => $currency,
                                    ]);
        } catch (Exception $e) {
            dd($e);
            Log::warning('Erro ao buscar dados - ReportsController - index');
            report($e);

            return redirect()->back();
        }
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
            if ($diffInDays <= 20) {
                return $this->getByDays($date, $projectId, $currency, $diffInDays);
            } else if ($diffInDays > 20 && $diffInDays <= 40) {
                return $this->getByTwentyDays($date, $projectId, $currency, $diffInDays);
            } else if ($diffInDays > 40 && $diffInDays <= 60) {
                return $this->getByFortyDays($date, $projectId, $currency, $diffInDays);
            } else if ($diffInDays > 60 && $diffInDays <= 140) {
                return $this->getByWeek($date, $projectId, $currency, $diffInDays);
            } else if ($diffInDays > 140) {
                return $this->getByMonth($date, $projectId, $currency, $diffInDays);
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
    private function getByDays($data, $projectId, $currency, $diffInDays)
    {
        try {

            $labelList    = [];
            $start        = $diffInDays;
            $dataFormated = new DateTime($data['startDate']);
            while ($start >= 0) {
                array_push($labelList, $dataFormated->format('d-m'));
                $dataFormated = $dataFormated->modify('+1 day');
                $start--;
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
    private function getByTwentyDays($date, $projectId, $currency, $diffInDays)
    {
        try {
            $labelList    = [];
            $start        = $diffInDays;
            $dataFormated = new DateTime($date['startDate']);
            $dataFormated = $dataFormated->modify('+1 day');

            while ($start >= 0) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->modify('+2 day');
                $start        -= 2;
                /*if ($dataFormated == $date['endDate']) {
                    break;
                }*/
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
    private function getByFortyDays($date, $projectId, $currency, $diffInDays)
    {
        try {
            $labelList    = [];
            $start        = $diffInDays;
            $dataFormated = new DateTime($date['startDate']);
            $dataFormated = $dataFormated->modify('+2 day');

            while ($start >= 0) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->modify('+3 day');
                $start        -= 3;
                if ($dataFormated == $date['endDate']) {
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

                    if (((Carbon::parse($order['date'])->subDays(2)->format('d/m') == $label) ||
                            (Carbon::parse($order['date'])->subDays(1)->format('d/m') == $label) ||
                            (Carbon::parse($order['date'])->format('d/m') == $label)) &&
                        (Carbon::parse($order['date'])->format('d/m') <= $label)
                    ) {

                        if ($order['payment_method'] == '1') {
                            $creditCardValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        } else {
                            $boletoValue += intval(preg_replace("/[^0-9]/", "", $order['value']));
                        }
                    }
                }

                array_push($creditCardData, substr(intval($creditCardValue), 0, -2));
                array_push($boletoData, substr(intval($boletoValue), 0, -2));
            }

            // 01-07
            // 03-07

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
    private function getByWeek($date, $projectId, $currency, $diffInDays)
    {
        try {
            $labelList    = [];
            $start        = $diffInDays;
            $dataFormated = new DateTime($date['startDate']);
            $dataFormated = $dataFormated->modify('+6 day');

            while ($start >= 0) {
                array_push($labelList, $dataFormated->format('d/m'));
                $dataFormated = $dataFormated->modify('+7 day');
                $start        -= 7;
                if ($dataFormated == $date['endDate']) {
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
    private function getByMonth($date, $projectId, $currency, $diffInDays)
    {
        try {
            $labelList    = [];
            $dataFormated = Carbon::parse($date['startDate']);
            $endDate      = Carbon::parse($date['endDate']);

            while ($dataFormated->format('m/y') <= $endDate->format('m/y')) {
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
