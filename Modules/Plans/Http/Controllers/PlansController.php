<?php

namespace Modules\Plans\Http\Controllers;

use App\Entities\Gift;
use App\Entities\Plan;
use App\Entities\PlanGift;
use App\Entities\Product;
use App\Entities\ProductPlan;
use App\Entities\Project;
use App\Entities\ZenviaSms;
use Auth;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Modules\Plans\Http\Requests\PlanStoreRequest;
use Modules\Plans\Http\Requests\PlanUpdateRequest;
use Modules\Plans\Transformers\PlansResource;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class PlansController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            $planModel    = new Plan();
            $projectModel = new Project();

            if ($request->has('project')) {
                $projectId = current(Hashids::decode($request->input('project')));

                if ($projectId) {
                    //hash ok
                    $project = $projectModel->find($projectId);

                    if (Gate::allows('edit', [$project])) {
                        //se pode editar o projeto pode visualizar os planos dele
                        $plans = $planModel->with([
                                                      'projectId.domains' => function($query) use ($projectId) {
                                                          $query->where([['project_id', $projectId], ['status', 3]])
                                                                ->first();
                                                      },
                                                  ])->where('project', $projectId);

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
    public function store(PlanStoreRequest $request)
    {
        try {
            $planModel    = new Plan();
            $productPlan  = new ProductPlan();
            $projectModel = new Project();

            $requestData            = $request->validated();
            $requestData['project'] = current(Hashids::decode($requestData['project']));
            $requestData['status']  = 1;

            $projectId = $requestData['project'];

            if ($projectId) {
                //hash ok

                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    $requestData['price'] = $this->getValue($requestData['price']);
                    if (!empty($requestData['products']) && !empty($requestData['product_amounts'])) {
                        $plan = $planModel->create($requestData);
                        $plan->update(['code' => $plan->id_code]);
                        foreach ($requestData['products'] as $keyProduct => $product) {
                            foreach ($requestData['product_amounts'] as $keyAmount => $productAmount) {
                                if ($keyProduct == $keyAmount) {
                                    $dataProductPlan = [
                                        'product' => $product,
                                        'plan'    => $plan->id,
                                        'amount'  => $productAmount,
                                    ];
                                    $productPlan->create($dataProductPlan);
                                }
                            }
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
     * @param PlanUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PlanUpdateRequest $request, $id)
    {
        try {
            $planModel    = new Plan();
            $productPlan  = new ProductPlan();
            $projectModel = new Project();

            $requestData = $request->validated();

            $projectId = current(Hashids::decode($requestData['project']));

            if ($projectId) {
                //hash ok

                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    unset($requestData['project']);
                    $planId               = Hashids::decode($id)[0];
                    $requestData['price'] = $this->getValue($requestData['price']);

                    $plan = $planModel->where('id', $planId)->first();
                    $plan->update($requestData);

                    $productPlans = $productPlan->where('plan', $plan->id)->get();
                    if (count($productPlans) > 0) {
                        foreach ($productPlans as $productPlan) {
                            $productPlan->forceDelete();
                        }
                    }
                    if (!empty($requestData['products']) && !empty($requestData['product_amounts'])) {
                        foreach ($requestData['products'] as $keyProduct => $product) {
                            foreach ($requestData['product_amounts'] as $keyAmount => $productAmount) {
                                if ($keyProduct == $keyAmount) {
                                    $productPlan->create([
                                        'product' => $product,
                                        'plan'    => $plan->id,
                                        'amount'  => $productAmount,
                                    ]);
                                }
                            }
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
    public function destroy($id)
    {
        try {

            $planModel = new Plan();

            if (isset($id)) {
                $planId = current(Hashids::decode($id));

                if ($planId) {
                    //hash Ok
                    $plan = $planModel->with(['productsPlans', 'plansSales', 'projectId'])
                                      ->where('id', $planId)
                                      ->first();

                    $project = $plan->projectId;

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
     * @param Request $request
     * @param $id
     * @return Factory|JsonResponse|View
     */
    public function show(Request $request, $id)
    {
        try {
            $planModel    = new Plan();
            $projectModel = new Project();

            $projectId = current(Hashids::decode($request->input('project')));
            if ($projectId) {
                //hash ok
                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    if (!empty($id)) {
                        $planId = current(Hashids::decode($id));
                        $plan   = $planModel->with([
                                                       'products', 'projectId.domains' => function($query) use ($projectId) {
                                $query->where([['project_id', $projectId], ['status', 3]])
                                      ->first();
                            },
                                                   ])->find($planId);

                        $plan->code = isset($plan->projectId->domains[0]->name) ? 'https://checkout.' . $plan->projectId->domains[0]->name . '/' . $plan->code : 'Dominio não configurado';

                        if (empty($plan)) {

                            return response()->json([
                                                        'message' => 'error',
                                                    ], 200);
                        } else {
                            $view = view('plans::details', ['plan' => $plan]);

                            return response()->json([
                                                        'message' => 'success',
                                                        'data'    => [
                                                            'view' => $view->render(),
                                                        ],
                                                    ], 200);
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
     * @param Request $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function create(Request $request)
    {
        try {
            $productModel = new Product();
            $projectModel = new Project();

            $projectId = current(Hashids::decode($request->input('project')));

            if ($projectId) {
                //hash ok
                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    if (!empty($project->shopify_id)) {
                        $products = $productModel->where('user', auth()->user()->id)
                                                 ->where('shopify', 1)
                                                 ->whereHas('productsPlans.plan', function($queryPlan) use($projectId) {
                                                     $queryPlan->where('project', $projectId);
                                                 })
                                                 ->get();
                    } else {
                        $products = $productModel->where('user', auth()->user()->id)
                                                 ->where('shopify', 0)
                                                 ->get();
                    }

                    if (count($products) > 0) {

                        $view = view('plans::create', [
                            'products' => $products,
                        ]);

                        return response()->json([
                                                    'message' => 'success',
                                                    'data'    => [
                                                        'view' => $view->render(),
                                                    ],
                                                ]);
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
            Log::error('Erro ao tentar acessar tela de cadastro (PlansController - create)');
            report($e);

            return response()->json([
                                        'message' => 'error',
                                    ], 200);
        }
    }

    /**
     * @param Request $request
     * @return Factory|JsonResponse|View
     */
    public function edit(Request $request)
    {
        try {
            $planModel    = new Plan();
            $productModel = new Product();
            $productPlan  = new ProductPlan();
            $projectModel = new Project();

            $projectId = current(Hashids::decode($request->input('project')));
            if ($projectId) {
                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    $planId = Hashids::decode($request->input('planId'))[0];
                    $plan   = $planModel->find($planId);
                    if ($plan) {

                        if (!empty($project->shopify_id)) {
                            $products = $productModel->where('user', auth()->user()->id)->where('shopify', 1)->get();
                        } else {
                            $products = $productModel->where('user', auth()->user()->id)->where('shopify', 0)->get();
                        }

                        $productPlans = $productPlan->where('plan', $plan->id)->get()->toArray();

                        return view('plans::edit', [
                            'plan'         => $plan,
                            'products'     => $products,
                            'productPlans' => $productPlans,
                        ]);
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
                return response()->json([
                                            'message' => 'error',
                                        ], 200);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar pixel (PlansController - edit)');
            report($e);

            return response()->json([
                                        'message' => 'error',
                                    ], 200);
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
}
