<?php

namespace Modules\Plans\Http\Controllers;

use App\Entities\Gift;
use App\Entities\Plan;
use App\Entities\PlanGift;
use App\Entities\Product;
use App\Entities\ProductPlan;
use App\Entities\Project;
use App\Entities\UserProject;
use App\Entities\ZenviaSms;
use Auth;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
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
            $planModel = new Plan();
            if ($request->has('project')) {
                $projectId = current(Hashids::decode($request->input('project')));
                $plans     = $planModel->with([
                                                  'projectId.domains' => function($query) use ($projectId) {
                                                      $query->where([['project_id', $projectId], ['status', 3]])
                                                            ->first();
                                                  },
                                              ])->where('project', $projectId);

                return PlansResource::collection($plans->orderBy('id', 'DESC')->paginate(5));
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
            $planModel   = new Plan();
            $productPlan = new ProductPlan();

            $requestData            = $request->validated();
            $requestData['project'] = current(Hashids::decode($requestData['project']));
            $requestData['status']  = 1;

            $requestData['price'] = $this->getValue($requestData['price']);

            $plan = $planModel->create($requestData);
            $plan->update(['code' => $plan->id_code]);
            if (isset($requestData['products']) && isset($requestData['product_amounts'])) {
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
        } catch (Exception $e) {
            Log::warning('Erro tentar salvar Plano (PlansController - store)');
            report($e);
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
            $planModel   = new Plan();
            $productPlan = new ProductPlan();

            $requestData = $request->validated();
            unset($requestData['project']);
            $planId               = Hashids::decode($id)[0];
            $requestData['price'] = $this->getValue($requestData['price']);

            $plan = $planModel->where('id', $planId)->first();
            $plan->update($requestData);

            $productPlans = $productPlan->where('plan', $plan->id)->get()->toArray();
            if (count($productPlans) > 0) {
                foreach ($productPlans as $productPlanArray) {
                    $productPlan->find($productPlanArray['id'])->delete();
                }
            }
            if (isset($requestData['products']) && isset($requestData['product_amounts'])) {
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

            return response()->json('Sucesso', 200);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do plano (PlansController - update)');
            report($e);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $planModel = new Plan();

        if (isset($id)) {
            $planId = current(Hashids::decode($id));

            $plan = $planModel->with(['productsPlans', 'plansSales'])
                              ->where('id', $planId)
                              ->first();

            if (count($plan->plansSales) > 0) {
                return response()->json(['message' => 'Impossível excluir, possui vendas associadas a este plano.'], 400);
            }
            if (count($plan->productsPlans) > 0) {
                foreach ($plan->productsPlans as $productPlan) {
                    $productPlan->delete();
                }
            }
            $planDeleted = $plan->delete();

            if ($planDeleted) {
                return response()->json(['Plano removido com sucesso'], 200);
            }
        }

        return response()->json(['message' => 'Impossível excluir, ocorreu um erro ao buscar dados do plano.'], 400);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Factory|JsonResponse|View
     */
    public function show(Request $request, $id)
    {
        try {
            $planModel = new Plan();

            $projectId = current(Hashids::decode($request->input('project')));

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
     */
    public function details(Request $request)
    {
        $planModel = new Plan();

        $requestData = $request->all();

        $plan = $planModel->with('project')->where('id', Hashids::decode($requestData['id_plano']))->first();

        $modalBody = '';

        $modalBody .= "<div class='col-xl-12 col-lg-12'>";
        $modalBody .= "<table class='table table-bordered table-hover table-striped'>";
        $modalBody .= "<thead>";
        $modalBody .= "</thead>";
        $modalBody .= "<tbody>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Nome:</b></td>";
        $modalBody .= "<td>" . $plan->name . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Descrição:</b></td>";
        $modalBody .= "<td>" . $plan->description . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Código identificador:</b></td>";
        $modalBody .= "<td>" . $plan->code . "</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Status:</b></td>";
        if ($plan->status == 1)
            $modalBody .= "<td>Ativo</td>";
        else
            $modalBody .= "<td>Inativo</td>";
        $modalBody .= "</tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<tr>";
        $modalBody .= "<td><b>Preço:</b></td>";
        $modalBody .= "<td>" . $plan->price . "</td>";
        $modalBody .= "</tr>";

        $produtosPlano = ProductPlan::where('plano', $plan->id)->get()->toArray();

        if (count($produtosPlano) > 0) {

            $modalBody .= "<tr class='text-center'>";
            $modalBody .= "<td colspan='2'><b>Produtos do plano:</b></td>";
            $modalBody .= "</tr>";

            foreach ($produtosPlano as $produtoPlano) {

                $produto   = Produto::find($produtoPlano['produto']);
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Produto:</b></td>";
                $modalBody .= "<td>" . $produto->name . "</td>";
                $modalBody .= "</tr>";
                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Quantidade:</b></td>";
                $modalBody .= "<td>" . $produtoPlano['product_amount'] . "</td>";
                $modalBody .= "</tr>";
            }
        }

        $planBrindes = PlanGift::where('plano', $plan->id)->get()->toArray();

        if (count($planBrindes) > 0) {

            $modalBody .= "<tr class='text-center'>";
            $modalBody .= "<td colspan='2'><b>Brindes do plano:</b></td>";
            $modalBody .= "</tr>";

            foreach ($planBrindes as $planBrinde) {

                $brinde = Gift::find($planBrinde['brinde']);

                $modalBody .= "<tr>";
                $modalBody .= "<td><b>Brinde:</b></td>";
                $modalBody .= "<td>" . $brinde->descricao . "</td>";
                $modalBody .= "</tr>";
            }
        }

        $modalBody .= "</thead>";
        $modalBody .= "</table>";
        $modalBody .= "<div class='text-center'>";
        if (!$plan->shopify_id) {
            $modalBody .= "<img src='" . url(CaminhoArquivosHelper::CAMINHO_FOTO_PLANO . $plan->foto) . "?dummy=" . uniqid() . "' style='height: 250px'>";
        } else {
            $modalBody .= "<img src='" . $plan->foto . "' style='height: 250px'>";
        }
        $modalBody .= "</div>";
        $modalBody .= "</div>";

        return response()->json($modalBody);
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

            $project = $projectModel->find(current(Hashids::decode($request->input('project'))));
            if (!empty($project->shopify_id)) {
                $products = $productModel->where('user', auth()->user()->id)->where('shopify', 1)->get();
            } else {
                $products = $productModel->where('user', auth()->user()->id)->where('shopify', 0)->get();
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
                                        ]);
            }
        } catch (Exception $e) {
            Log::error('Erro ao tentar acessar tela de cadastro (PlansController - create)');
            report($e);
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


            $planId = Hashids::decode($request->input('planId'))[0];
            $plan   = $planModel->find($planId);
            if ($plan) {

                $project = $projectModel->find(current(Hashids::decode($request->input('project'))));
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
            }

            return response()->json('erro');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar tela editar pixel (PlansController - edit)');
            report($e);
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
