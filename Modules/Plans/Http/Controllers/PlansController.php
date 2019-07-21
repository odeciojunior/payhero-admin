<?php

namespace Modules\Plans\Http\Controllers;

use App\Entities\Gift;
use App\Entities\Plan;
use App\Entities\PlanGift;
use App\Entities\Product;
use App\Entities\ProductPlan;
use App\Entities\UserProject;
use App\Entities\ZenviaSms;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Core\Helpers\CaminhoArquivosHelper;
use Modules\Plans\Http\Requests\PlanStoreRequest;
use Modules\Plans\Http\Requests\PlanUpdateRequest;
use Modules\Plans\Transformers\PlansResource;
use Vinkla\Hashids\Facades\Hashids;

class PlansController extends Controller
{
    private $planModel;
    private $productModel;
    private $userProjectModel;
    private $productPlanModel;
    private $zenviaSmsModel;

    private function getPlan()
    {
        if (!$this->planModel) {
            $this->planModel = app(Plan::class);
        }

        return $this->planModel;
    }

    private function getProduct()
    {
        if (!$this->productModel) {
            $this->productModel = app(Product::class);
        }

        return $this->productModel;
    }

    private function getUserProject()
    {
        if (!$this->userProjectModel) {
            $this->userProjectModel = app(UserProject::class);
        }

        return $this->userProjectModel;
    }

    private function getProductPlan()
    {
        if (!$this->productPlanModel) {
            $this->productPlanModel = app(ProductPlan::class);
        }

        return $this->productPlanModel;
    }

    private function getZenviaSms()
    {
        if (!$this->zenviaSmsModel) {
            $this->zenviaSmsModel = app(ZenviaSms::class);
        }

        return $this->zenviaSmsModel;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        try {
            if ($request->has('project')) {
                $projectId = current(Hashids::decode($request->input('project')));
                $plans     = $this->getPlan()->with([
                                                        'projectId.domains' => function($query) use ($projectId) {
                                                            $query->where([['project_id', $projectId], ['status', 3]])
                                                                  ->first();
                                                        },
                                                    ])->where('project', $projectId)->get();

                return PlansResource::collection($plans);
            }
        } catch (Exception $e) {
            Log::warning('Erro ao tentar buscar planos (PlansController - index)');
            report($e);
        }
    }

    /**
     * @param PlanStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PlanStoreRequest $request)
    {
        try {
            $requestData            = $request->validated();
            $requestData['project'] = current(Hashids::decode($requestData['project']));
            $requestData['status']  = 1;

            $requestData['price'] = $this->getValue($requestData['price']);

            $plan = $this->getPlan()->create($requestData);
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
                            $this->getProductPlan()->create($dataProductPlan);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PlanUpdateRequest $request, $id)
    {
        try {
            $requestData = $request->validated();
            unset($requestData['project']);
            $planId               = Hashids::decode($id)[0];
            $requestData['price'] = $this->getValue($requestData['price']);

            $plan = $this->getPlan()->where('id', $planId)->first();
            $plan->update($requestData);

            $productPlans = $this->getProductPlan()->where('plan', $plan->id)->get()->toArray();
            if (count($productPlans) > 0) {
                foreach ($productPlans as $productPlan) {
                    $this->getProductPlan()->find($productPlan['id'])->delete();
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
                            $this->getProductPlan()->create($dataProductPlan);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        if (isset($id)) {
            $planId = current(Hashids::decode($id));

            $plan = $this->getPlan()->with(['productsPlans', 'plansSales'])
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        try {
            $projectId = current(Hashids::decode($request->input('project')));

            if (isset($id)) {
                $planId = Hashids::decode($id)[0];
                //                $plan   = $this->getPlan()->with(['products', 'projectId.domains' => function($query) use ($projectId) {
                //                    $query->where([['project_id', $projectId], ['status', 3]])
                //                          ->first();
                //                },])->find($planId);
                $plan = $this->getPlan()->with([
                                                   'products', 'projectId.domains' => function($query) use ($projectId) {
                        $query->where([['project_id', $projectId], ['status', 3]])
                              ->first();
                    },
                                               ])->find($planId);

                return view('plans::details', ['plan' => $plan]);
            }

            return response()->json('Erro ao buscar Plano');
        } catch (Exception $e) {
            Log::warning('Erro ao tentar acessar detalhes do Plano (PlansController - show)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function details(Request $request)
    {

        $requestData = $request->all();

        $plan = Plan::where('id', Hashids::decode($requestData['id_plano']))->first();

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        try {

            $products = $this->getProduct()->where('user', \Auth::user()->id)->where('shopify', 0)->get();

            return view('plans::create', [
                'products' => $products,
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao tentar acessar tela de cadastro (PlansController - create)');
            report($e);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        try {
            $planId = Hashids::decode($request->input('planId'))[0];
            $plan   = $this->getPlan()->find($planId);
            if ($plan) {
                $products     = $this->getProduct()->where('user', \Auth::user()->id)->where('shopify', 0)->get()
                                     ->toArray();
                $productPlans = $this->getProductPlan()->where('plan', $plan->id)->get()->toArray();

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
