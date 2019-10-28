<?php

namespace Modules\Plans\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Plans\Http\Requests\PlanStoreRequest;
use Modules\Plans\Http\Requests\PlanUpdateRequest;
use Modules\Plans\Transformers\PlansDetailsResource;
use Modules\Plans\Transformers\PlansResource;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Modules\Planos\Transformers\PlanosResource;

class PlansApiController extends Controller
{
    /**
     * @param $projectId
     * @param Request $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index($projectId, Request $request)
    {
        try {
            $planModel    = new Plan();
            $projectModel = new Project();
            if (!empty($projectId)) {
                $projectId = current(Hashids::decode($projectId));

                if ($projectId) {
                    //hash ok
                    $project = $projectModel->find($projectId);

                    if (Gate::allows('edit', [$project])) {
                        //se pode editar o projeto pode visualizar os planos dele
                        $plans = $planModel->with([
                                                      'project.domains' => function($query) use ($projectId) {
                                                          $query->where([['project_id', $projectId], ['status', 3]])
                                                                ->first();
                                                      },
                                                  ])->where('project_id', $projectId);
                        if ($request->has('plan') && !empty($request->input('plan'))) {
                            $plans->where('name', 'like', '%' . $request->input('plan') . '%');
                        }

                        return PlansResource::collection($plans->orderBy('id', 'DESC')->paginate(5));
                    } else {
                        return response()->json([
                                                    'message' => 'Sem permissão para visualizar planos',
                                                ], 403);
                    }
                } else {
                    //hash errado
                    return response()->json([
                                                'message' => 'Projeto não encontrado',
                                            ], 400);
                }
            } else {
                return response()->json([
                                            'message' => 'Projeto não encontrado',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar planos (PlansController - index)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao tentar listar planos',
                                    ], 400);
        }
    }

    /**
     * @param PlanStoreRequest $request
     * @return JsonResponse
     */
    public function store(PlanStoreRequest $request, $projectID)
    {
        try {
            $planModel    = new Plan();
            $productPlan  = new ProductPlan();
            $projectModel = new Project();

            $requestData               = $request->validated();
            $requestData['project_id'] = current(Hashids::decode($requestData['project_id']));
            $requestData['status']     = 1;

            $projectId = $requestData['project_id'];

            if ($projectId) {
                //hash ok

                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {
                    $requestData['price'] = number_format(intval(preg_replace("/[^0-9]/", "", $requestData['price'])) / 100, 2, ',', '.');
                    $requestData['price'] = $this->getValue($requestData['price']);
                    if (!empty($requestData['products']) && !empty($requestData['product_amounts'])) {
                        $plan = $planModel->create($requestData);
                        if (!empty($plan)) {
                            $plan->update(['code' => $plan->id_code]);
                            foreach ($requestData['products'] as $keyProduct => $product) {

                                $requestData['product_cost'][$keyProduct] = preg_replace("/[^0-9]/", "", $requestData['product_cost'][$keyProduct]);

                                $productPlan->create([
                                                         'product_id'         => $requestData['products'][$keyProduct],
                                                         'plan_id'            => $plan->id,
                                                         'amount'             => $requestData['product_amounts'][$keyProduct] ?? 1,
                                                         'cost'               => $requestData['product_cost'][$keyProduct] ?? 0,
                                                         'currency_type_enum' => $productPlan->present()
                                                                                             ->getCurrency($requestData['currency'][$keyProduct]),
                                                     ]);
                            }
                        } else {
                            return response()->json([
                                                        'message' => 'Ocorreu um erro, tente novamente mais tarde',
                                                    ], 400);
                        }
                    }

                    return response()->json('Plano Configurado com sucesso!', 200);
                } else {
                    return response()->json([
                                                'message' => 'Sem permissão para salvar este plano',
                                            ], 403);
                }
            } else {
                //hash errado

                return response()->json([
                                            'message' => 'Projeto não encontrado',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro tentar salvar Plano (PlansController - store)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao salvar plano',
                                    ], 400);
        }
    }

    /**
     * @param $projectID
     * @param $id
     * @return JsonResponse|PlansDetailsResource
     */
    public function show($projectID, $id)
    {
        try {
            $planModel    = new Plan();
            $projectModel = new Project();

            $projectId = current(Hashids::decode($projectID));

            if ($projectId) {
                //hash ok
                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    if (!empty($id)) {
                        $planId = current(Hashids::decode($id));
                        $plan   = $planModel->with([
                                                       'productsPlans.product', 'project.domains' => function($query) use ($projectId) {
                                $query->where([['project_id', $projectId], ['status', 3]])
                                      ->first();
                            },
                                                   ])->find($planId);

                        if (empty($plan)) {
                            return response()->json([
                                                        'message' => 'error',
                                                    ], 200);
                        } else {
                            return new PlansDetailsResource($plan);
                        }
                    } else {
                        return response()->json([
                                                    'message' => 'error',
                                                ], 200);
                    }
                } else {
                    return response()->json([
                                                'message' => 'error',
                                            ], 200);
                }
            } else {
                //hash errado
                return response()->json([
                                            'message' => 'error',
                                        ], 200);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do Plano (PlansController - show)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao buscar dados do plano!',
                                    ], 400);
        }
    }

    /**
     * @param PlanUpdateRequest $request
     * @param $projectID
     * @param $id
     * @return JsonResponse
     */
    public function update(PlanUpdateRequest $request, $projectID, $id)
    {
        try {
            $planModel    = new Plan();
            $productPlan  = new ProductPlan();
            $projectModel = new Project();

            $requestData = $request->validated();
            $projectId   = current(Hashids::decode($projectID));

            if ($projectId) {
                //hash ok

                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    unset($requestData['project_id']);
                    $planId               = Hashids::decode($id)[0];
                    $requestData['price'] = number_format(intval(preg_replace("/[^0-9]/", "", $requestData['price'])) / 100, 2, ',', '.');
                    $requestData['price'] = $this->getValue($requestData['price']);

                    $plan = $planModel->where('id', $planId)->first();

                    $plan->update([
                                      'name'        => $requestData["name"],
                                      'description' => $requestData["description"],
                                      'code'        => $id,
                                      'price'       => $requestData["price"],
                                      'status'      => $planModel->present()->getStatus('active'),
                                  ]);

                    $productPlans = $productPlan->where('plan_id', $plan->id)->get();
                    if (count($productPlans) > 0) {
                        foreach ($productPlans as $productPlan) {
                            $productPlan->forceDelete();
                        }
                    }
                    if (!empty($requestData['products']) && !empty($requestData['product_amounts'])) {
                        foreach ($requestData['products'] as $keyProduct => $product) {

                            $requestData['product_cost'][$keyProduct] = preg_replace("/[^0-9]/", "", $requestData['product_cost'][$keyProduct]);

                            $productPlan->create([
                                                     'product_id'         => $requestData['products'][$keyProduct],
                                                     'plan_id'            => $plan->id,
                                                     'amount'             => $requestData['product_amounts'][$keyProduct] ?? 1,
                                                     'cost'               => $requestData['product_cost'][$keyProduct] ?? 0,
                                                     'currency_type_enum' => $productPlan->present()
                                                                                         ->getCurrency($requestData['currency'][$keyProduct]),
                                                 ]);
                        }
                    }

                    return response()->json('Sucesso', 200);
                } else {
                    return response()->json([
                                                'message' => 'Sem permissão para atualizar este plano',
                                            ], 403);
                }
            } else {
                //hash errado
                return response()->json([
                                            'message' => 'Projeto não encontrado',
                                        ], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do plano (PlansController - update)');
            dd($e);
            report($e);

            return response()->json([
                                        'message' => 'Erro ao atualizar plano',
                                    ], 400);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($projectId, $id)
    {
        try {

            $planModel = new Plan();

            if (isset($id)) {
                $planId = current(Hashids::decode($id));

                if ($planId) {
                    //hash Ok
                    $plan = $planModel->with(['productsPlans', 'plansSales', 'project'])
                                      ->where('id', $planId)
                                      ->first();

                    $project = $plan->project;
                    if (Gate::allows('edit', [$project])) {

                        if (count($plan->plansSales) > 0) {
                            return response()->json(['message' => 'Impossível excluir, possui vendas associadas a este plano.'], 400);
                        }
                        if (count($plan->productsPlans) > 0) {
                            foreach ($plan->productsPlans as $productPlan) {
                                $productPlan->forceDelete();
                            }
                        }
                        $planDeleted = $plan->delete();

                        if ($planDeleted) {
                            return response()->json(['message' => 'Plano removido com sucesso'], 200);
                        }
                    } else {
                        return response()->json(['message' => 'Sem permissão para excluir plano'], 400);
                    }
                } else {
                    //Hash errado
                    return response()->json(['message' => 'Erro ao excluir plano.'], 400);
                }
            } else {
                return response()->json(['message' => 'Impossível excluir, ocorreu um erro ao buscar dados do plano.'], 400);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do Plano (PlansController - show)');
            report($e);

            return response()->json([
                                        'message' => 'Erro ao buscar dados do plano!',
                                    ], 400);
        }
    }

    /**
     * @param $str
     * @return mixed|string
     */
    function getValue($str)
    {

        if ($str == '') {
            return '0.00';
        }

        if (strstr($str, ",")) {
            $str = str_replace(".", "", $str);
            $str = str_replace(",", ".", $str);
        }

        $arrayValor = explode('.', $str);

        if (count($arrayValor) == 1) {
            $str = $str . '.00';
        } else {
            if (strlen($arrayValor['1']) == 1) {
                $str .= '0';
            }
        }

        return $str;
    }
    /*public function getPlanName(Request $request)
    {
        try{

        }catch (Exception $e){
            Log::warning('Erro ao buscar ')
        }
    }*/
}
