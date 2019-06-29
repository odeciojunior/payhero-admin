<?php

namespace Modules\Reports\Http\Controllers;

use App\Entities\Project;
use App\Entities\Sale;
use App\Entities\UserProject;
use Exception;
use Illuminate\Http\Request;
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

    public function index()
    {
        try {
            $user = auth()->user();
            $userProjects = $this->getUserProjects()->with(['projectId'])->where('user', $user->id)->get();

            $projects = [];
            foreach ($userProjects as $userProject) {
                if (isset($userProject->projectId)) {
                    $projects [] = $userProject->projectId;
                }
            }

            return view('reports::index', compact('projects'));
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
            $msg = 'Ocorreu um erro';
            if (isset($dataSearch['project'])) {
                $projectId = current(Hashids::decode($dataSearch['project']));
                $sales = $this->getSales()
                    ->where([['project', $projectId], ['owner', auth()->user()->id]])
                    ->whereBetween('start_date', [$dataSearch['startDate'], [$dataSearch['endDate']]])
                    ->get();

                $contBoleto = 0;
                $contRecused = 0;
                $contAproved = 0;
                $contChargeBack = 0;


                $totalPercentPaidCredit = 0;
                $totalPercentPaidBoleto = 0;

                $totalPaidValueAproved = 0;

                $totalValueBoleto = 0;
                $totalValueCreditCard = 0;

                if (isset($sales) && count($sales) > 0) {
                    foreach ($sales as $sale) {

                        // cartao
                        if ($sale->payment_method == 1 && $sale->status == 1) {
                            $totalValueCreditCard += $sale->total_paid_value;
                        }
                        if ($sale->payment_method == 2 && $sale->status == 1) {
                            $totalValueBoleto += $sale->total_paid_value;
                        }
                        // boleto
                        if ($sale->payment_method == 2) {
                            $contBoleto++;
                        }

                        // vendas aprovadas
                        if ($sale->status == 1) {
                            $totalPaidValueAproved += $sale->total_paid_value;
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

                    $totalPaidValueAproved = number_format(intval($totalPaidValueAproved), 2, ',', '.');
                    $totalValueBoleto = number_format(intval($totalValueBoleto), 2, ',', ' . ');
                    $totalValueCreditCard = number_format(intval($totalValueCreditCard), 2, ',', '.');
                    return response()->json([
                        'totalPaidValueAproved' => $totalPaidValueAproved,
                        'contAproved' => $contAproved,
                        'contBoleto' => $contBoleto,
                        'contRecused' => $contRecused,
                        'contChargeBack' => $contChargeBack,
                        'totalPercentCartao' => $totalPercentPaidCredit,
                        'totalPercentPaidBoleto' => $totalPercentPaidBoleto,
                        'totalValueBoleto' => $totalValueBoleto,
                        'totalValueCreditCard' => $totalValueCreditCard,
                    ]);
                }
                $msg = 'Este projeto não tem vendas no periodo selecionado';
            }
            return response()->json(['msg' => $msg]);
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados - ReportsController - index');
            report($e);
            return redirect()->back();
        }
    }
}
