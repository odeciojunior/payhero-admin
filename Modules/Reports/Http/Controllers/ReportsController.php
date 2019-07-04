<?php

namespace Modules\Reports\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Entities\Sale;
use App\Entities\Company;
use App\Entities\Project;
use Illuminate\Http\Request;
use App\Entities\UserProject;
use Illuminate\Routing\Controller;
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

    public function getSales()
    {
        if (!$this->salesModel) {
            $this->salesModel = app(Sale::class);
        }

        return $this->salesModel;
    }

    public function getCompany()
    {
        if (!$this->companyModel) {
            $this->companyModel = app(Company::class);
        }

        return $this->companyModel;
    }

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

    public function getValues(Request $request)
    {
        try {
            $dataSearch = $request->all();

            $projectId = current(Hashids::decode($dataSearch['project']));

            $userProject = $this->getUserProjects()->where([
                                                               ['user', \Auth::user()->id],
                                                               ['type', 'producer'],
                                                               ['project', $projectId],
                                                           ])->first();

            if (isset($dataSearch['project'])) {
                $sales = $this->getSales()
                              ->select('sales.*', 'transaction.value')
                              ->leftJoin('transactions as transaction', function($join) use ($userProject) {
                                  $join->where('transaction.company', '=', $userProject->company);
                                  $join->on('transaction.sale', '=', 'sales.id');
                              })
                              ->where([['project', $projectId], ['owner', auth()->user()->id]])
                    //->whereBetween('start_date', [$dataSearch['startDate'], [$dataSearch['endDate']]])
                              ->whereDate('start_date', $dataSearch['startDate'])
                              ->get();

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

    private function getChartData($data, $projectId, $currency)
    {

        if ($data['startDate'] == $data['endDate']) {

            date_default_timezone_set('America/Sao_Paulo');

            if (Carbon::parse($data['startDate'])->format('"m/d/y') == Carbon::now()->format('"m/d/y')) {

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
                        if ($order['payment_method'] == '1') {
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
