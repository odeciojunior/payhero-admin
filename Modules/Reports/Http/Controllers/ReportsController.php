<?php

namespace Modules\Reports\Http\Controllers;

use App\Entities\Project;
use App\Entities\Sale;
use App\Entities\UserProject;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
        /**
         * 1 - parovado
         * 2- pendente
         * 3 - recusado
         * 4 chargeback
         */

        $user         = auth()->user();
        $userProjects = $this->getUserProjects()->with(['projectId'])->where('user', $user->id)->get();

        $projects = [];
        foreach ($userProjects as $userProject) {
            if (isset($userProject->projectId)) {
                $projects [] = $userProject->projectId;
            }
        }

        return view('reports::index', compact('projects'));
    }

    public function getValues(Request $request)
    {
        $dataSearch = $request->all();
        if (isset($dataSearch['project'])) {

            $projectId      = current(Hashids::decode($dataSearch['project']));
            $sales          = $this->getSales()
                                   ->where([['project', $projectId], ['owner', auth()->user()->id]])
                //                                           ->whereBetween('start_date', [$dataSearch['end'], $dataSearch['start']])
                                   ->get();
            $contBoleto     = 0;
            $contRecused    = 0;
            $contAproved    = 0;
            $contChargeBack = 0;

            $contCreditCard = 0;

            $totalPercentPaidCredit = 0;
            $totalPercentPaidBoleto = 0;

            $totalPaidValue = 0;

            $totalValueBoleto = 0;

            $totalValueCreditCard = 0;

            //conta aprovados no cartao
            $contCreditCardAproved = 0;
            if (isset($sales) && count($sales) > 0) {
                foreach ($sales as $sale) {
                    if ($sale->payment_method == 1) {
                        $contCreditCard++;
                    }
                    if ($sale->payment_method == 1 && $sale->status == 1) {
                        $totalValueCreditCard += $sale->total_paid_value;
                        $contCreditCardAproved++;
                    }

                    if ($sale->payment_method == 2) {
                        $contBoleto++;
                    }

                    if ($sale->payment_method == 2 && $sale->status == 1) {
                        $totalValueBoleto += $sale->total_paid_value;
                    }

                    if ($sale->status == 1) {
                        $totalPaidValue += $sale->total_paid_value;
                    }

                    if ($sale->status == 1) {
                        $contAproved++;
                    }
                    if ($sale->status == 3) {
                        $contRecused++;
                    }
                    if ($sale->status == 4) {
                        $contChargeBack++;
                    }
                }

                if ($totalPaidValue != 0) {
                    $totalPercentPaidCredit = number_format((intval($totalValueCreditCard) * 100) / intval($totalPaidValue), 2, ',', ' . ');
                    $totalPercentPaidBoleto = number_format((intval($totalValueBoleto) * 100) / intval($totalPaidValue), 2, ',', ' . ');
                }

                $totalValueBoleto     = number_format(intval($totalValueBoleto), 2, ',', ' . ');
                $totalValueCreditCard = number_format(intval($totalValueCreditCard), 2, ',', ' . ');
            }

            return response()->json([
                                        'totalPaidValue'         => $totalPaidValue,
                                        'contAproved'            => $contAproved,
                                        'contBoleto'             => $contBoleto,
                                        'contRecused'            => $contRecused,
                                        'contChargeBack'         => $contChargeBack,
                                        'totalPercentCartao'     => $totalPercentPaidCredit,
                                        'totalPercentPaidBoleto' => $totalPercentPaidBoleto,
                                        'totalValueBoleto'       => $totalValueBoleto,
                                        'totalValueCreditCard'   => $totalValueCreditCard,

                                        /*'totalValueBoleto' => $totalValueBoleto,
                                        'totalValueBoleto' => $totalValueBoleto,*/
                                    ]);
        }
    }
}
