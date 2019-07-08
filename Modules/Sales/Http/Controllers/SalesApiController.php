<?php

namespace Modules\Sales\Http\Controllers;

use App\Entities\Plan;
use App\Entities\PlanSale;
use App\Entities\Project;
use App\Entities\Sale;
use App\Entities\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Sales\Transformers\SaleApiResource;
use Modules\Sales\Transformers\SalesResource;

class SalesApiController extends Controller
{
    /**
     * @var Sale
     */
    private $saleModel;
    /**
     * @var User
     */
    private $userModel;
    /**
     * @var Project
     */
    private $projectModel;
    /**
     * @var
     */
    private $planModel;
    /**
     * @var
     */
    private $plansSalesModel;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getSaleModel()
    {
        if (!$this->saleModel) {
            $this->saleModel = app(Sale::class);
        }

        return $this->saleModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getUserModel()
    {
        if (!$this->userModel) {
            $this->userModel = app(User::class);
        }

        return $this->userModel;
    }

    private function getProjectModel()
    {
        if (!$this->projectModel) {
            $this->projectModel = app(Project::class);
        }

        return $this->projectModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getPlan()
    {
        if (!$this->planModel) {
            $this->planModel = app(Plan::class);
        }

        return $this->planModel;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    private function getPlansSales()
    {
        if (!$this->plansSalesModel) {
            $this->plansSalesModel = app(PlanSale::class);
        }

        return $this->plansSalesModel;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($data)
    {
        try {
            if ($data == 'M9NBQY3P') {

                $userZmDeals = $this->getUserModel()->where('email', 'fernandomuniz1337@gmail.com')->first();

                $sales = $this->getSaleModel()->where([
                                                          ['owner', $userZmDeals->id],
                                                          //['status', '!=', 3],
                                                      ]);

                //paymentmethod = 2 boleto

                $project = $this->getProjectModel()->where('name', 'zmdeals')->first();

                $plans    = $this->getPlan()->where('project', $project->id)->pluck('id');
                $salePlan = $this->getPlansSales()->whereIn('plan', $plans)->pluck('sale');
                $sales->whereIn('id', $salePlan);

                $sales->orderBy('id', 'DESC');

                return SaleApiResource::collection($sales->get());
            } else {
                return response()->json(['message' => 'Erro'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao buscar vendas SalesApiController - index');
            report($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('sales::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('sales::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('sales::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
