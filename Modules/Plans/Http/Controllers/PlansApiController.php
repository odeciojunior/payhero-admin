<?php

namespace Modules\Plans\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Core\Entities\AffiliateLink;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\PlanService;
use Modules\Plans\Http\Requests\PlanStoreRequest;
use Modules\Plans\Http\Requests\PlanUpdateInformationsRequest;
use Modules\Plans\Http\Requests\PlanUpdateProductsRequest;
use Modules\Plans\Http\Requests\PlanUpdateRequest;
use Modules\Plans\Transformers\PlansDetailsResource;
use Modules\Plans\Transformers\PlansResource;
use Modules\Plans\Transformers\PlansSelectResource;
use Spatie\Activitylog\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;

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

            $planModel = new Plan();
            $projectModel = new Project();

            activity()->on($planModel)->tap(function (Activity $activity) {
                $activity->log_name = 'visualization';
            })->log('Visualizou tela todos os planos');

            if (!empty($projectId)) {
                $projectId = current(Hashids::decode($projectId));

                if ($projectId) {
                    //hash ok
                    $project = $projectModel->find($projectId);

                    if (Gate::allows('edit', [$project])) {
                        //se pode editar o projeto pode visualizar os planos dele
                        $plans = $planModel->with([
                            'productsPlans.product', 'project.domains' => function($query) use ($projectId) {
                                $query->where('project_id', $projectId)
                                    ->where('status', 3)
                                    ->first();
                            },
                        ])->where('project_id', $projectId);

                        if (!empty($request->input('plan'))) {
                            $plans = $plans->where(
                                function ($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->input('plan') . '%')
                                        ->orWhere('price', 'like', '%' . str_replace(array('R', '$', ' ', '.', ','), array('', '', '', '', '.'), $request->input('plan')) . '%')
                                        ->orWhere('description', 'like', '%' . $request->input('plan') . '%');
                                }
                            );
                        }

                        if ($project->status == Project::STATUS_ACTIVE) {
                            $plans = $plans->where('status', Plan::STATUS_ACTIVE);

                        } else {
                            $plans = $plans->where('status', Plan::STATUS_DESABLE);
                        }

                        $plans = $plans->orderBy('id', 'DESC')->paginate(5);

                        return PlansResource::collection($plans);
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
            $planModel          = new Plan();
            $productPlan        = new ProductPlan();
            $productModel       = new Product();
            $projectModel       = new Project();
            $affiliateLinkModel = new AffiliateLink();
            $planService        = new PlanService();

            $requestData = $request->validated();
            $requestData['project_id'] = current(Hashids::decode($requestData['project_id']));
            $requestData['status'] = 1;

            $projectId = $requestData['project_id'];

            if ($projectId) {
                $project = $projectModel->with('affiliates', 'affiliates.user')->find($projectId);

                if (Gate::allows('edit', [$project])) {
                    $requestData['price'] = number_format(intval(preg_replace("/[^0-9]/", "", $requestData['price'])) / 100, 2, ',', '.');
                    $requestData['price'] = $this->getValue($requestData['price']);
                    if (!empty($requestData['products'])) {
                        $get_product = $productModel->find(current(Hashids::decode($request['products'][0]['id'])));

                        $requestData['name'] = FoxUtils::removeSpecialChars($requestData['name']);
                        $requestData['description'] = FoxUtils::removeSpecialChars($requestData['description']);

                        $plan = $planModel->create([
                            'project_id'            => $projectId,
                            'name'                  => FoxUtils::removeSpecialChars($request['name']),
                            'description'           => FoxUtils::removeSpecialChars($request['description']),
                            'price'                 => $requestData['price'],
                            'status'                => 1,
                            'shopify_id'            => $get_product->shopify_id ?? null,
                            'shopify_variant_id'    => $get_product->shopify_variant_id ?? null
                        ]);

                        if (!empty($plan)) {
                            $plan->update(['code' => $plan->id_code]);
                            foreach ($request['products'] as $product) {
                                $productPlan->create([
                                    'product_id'         => current(Hashids::decode($product['id'])),
                                    'plan_id'            => $plan->id,
                                    'amount'             => $product['amount'] ?? 1,
                                    'cost'               => $product['value'] ? preg_replace("/[^0-9]/", "", $product['value']) : 0,
                                    'currency_type_enum' => $productPlan->present()->getCurrency($product['currency_type_enum']),
                                ]);
                            }

                            if (count($project->affiliates) > 0) {
                                foreach ($project->affiliates as $affiliate) {
                                    $affiliateHash = Hashids::connection('affiliate')->encode($affiliate->id);
                                    $affiliateLinkModel->create([
                                        'affiliate_id' => $affiliate->id,
                                        'plan_id' => $plan->id,
                                        'parameter' => $affiliateHash . Hashids::connection('affiliate')->encode($plan->id),
                                        'clicks_amount' => 0,
                                        'link' => $planService->getCheckoutLink($plan),
                                    ]);
                                }
                            }
                        } else {
                            return response()->json([
                                'message' => 'Ocorreu um erro, tente novamente mais tarde',
                            ], 400);
                        }

                        return response()->json('Plano Configurado com sucesso!', 200);
                    } else {
                        return response()->json([
                            'message' => 'Ocorreu um erro, tente novamente mais tarde',
                        ], 400);
                    }
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
            Log::warning('Erro ao tentar salvar Plano (PlansController - store)');
            report($e);

            return response()->json([
                'message' => 'Erro ao salvar plano',
            ], 400);
        }
    }

    public function show($projectID, $id)
    {
        try {
            $planModel = new Plan();
            $projectModel = new Project();

            activity()->on($planModel)->tap(function (Activity $activity) use ($id) {
                $activity->log_name = 'visualization';
                $activity->subject_id = current(Hashids::decode($id));
            })->log('Visualizou tela detalhes do plano');

            $projectId = current(Hashids::decode($projectID));

            if ($projectId) {
                //hash ok
                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    if (!empty($id)) {
                        $planId = current(Hashids::decode($id));

                        $plan = $planModel->with([
                            'productsPlans' => function ($query) use ($planId) {
                                $query->where('plan_id', $planId);
                            },
                            'productsPlans.product',
                            'project.domains' => function ($query) use ($projectId) {
                                $query->where([['project_id', $projectId], ['status', 3]])->first();
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
            $planModel = new Plan();
            $productPlan = new ProductPlan();
            $projectModel = new Project();

            $requestData = $request->validated();
            $projectId = current(Hashids::decode($projectID));

            if ($projectId) {
                //hash ok

                $project = $projectModel->find($projectId);

                if (Gate::allows('edit', [$project])) {

                    unset($requestData['project_id']);
                    $planId = Hashids::decode($id)[0];

                    PlanService::forgetCache($planId);

                    $requestData['price'] = number_format(intval(preg_replace("/[^0-9]/", "", $requestData['price'])) / 100, 2, ',', '.');
                    $requestData['price'] = $this->getValue($requestData['price']);

                    $plan = $planModel->with('plansSales')->where('id', $planId)->first();

                    $plan->update([
                        'name' => FoxUtils::removeSpecialChars($requestData['name']),
                        'description' => FoxUtils::removeSpecialChars($requestData['description']),
                        'code' => $id,
                        'price' => $requestData["price"],
                        'status' => $planModel->present()->getStatus('active'),
                    ]);

                    $productPlans = $productPlan->where('plan_id', $plan->id)->get();
                    $productPlanId = $productPlans->pluck('product_id')->toArray();
                    $productPlanAmount = $productPlans->pluck('amount')->toArray();
                    $resultId = array_diff($productPlanId, $requestData['products']);
                    $resultAmount = array_diff($productPlanAmount, $requestData['product_amounts']);

                    if (count($plan->plansSales) > 0 && (!empty($resultId) || !empty($resultAmount)) || (count($requestData['products']) > count($productPlanId))) {
                        return response()->json(['message' => 'Impossível editar os produtos do plano pois possui vendas associadas.'], 400);
                    }

                    $idsProductPlan = [];
                    $objProductPlan = new ProductPlan();
                    if (!empty($requestData['products']) && !empty($requestData['product_amounts'])) {
                        foreach ($requestData['products'] as $keyProduct => $product) {
                            if (empty($requestData['product_cost'][$keyProduct]) || $requestData['product_cost'][$keyProduct] == '0.00') {
                                $requestData['product_cost'][$keyProduct] = 0;
                            } else {
                                $requestData['product_cost'][$keyProduct] = preg_replace("/[^0-9]/", "", $requestData['product_cost'][$keyProduct]);
                            }

                            $productPlan = ProductPlan::where('product_id', $requestData['products'][$keyProduct])->where('plan_id', $plan->id)->first();
                            if (!empty($productPlan)) {
                                $productPlan->amount = $requestData['product_amounts'][$keyProduct] ?? 1;
                                $productPlan->cost = $requestData['product_cost'][$keyProduct] ?? 0;
                                $productPlan->currency_type_enum = $productPlan->present()->getCurrency($requestData['currency'][$keyProduct]);
                                $productPlan->update();
                                $idsProductPlan[] = $productPlan->id;
                            } else {
                                $productPlan = ProductPlan::create([
                                    'product_id' => $requestData['products'][$keyProduct],
                                    'plan_id' => $plan->id,
                                    'amount' => $requestData['product_amounts'][$keyProduct] ?? 1,
                                    'cost' => $requestData['product_cost'][$keyProduct] ?? 0,
                                    'currency_type_enum' => $objProductPlan->present()
                                        ->getCurrency($requestData['currency'][$keyProduct]),
                                ]);
                                $idsProductPlan[] = $productPlan->id;
                            }

                            $productId = $requestData['products'][$keyProduct];
                            CacheService::forget(CacheService::CHECKOUT_CART_PRODUCT_PLAN, $productId);
                        }
                    }

                    if (count($idsProductPlan) > 0) {
                        $rowsProductPlan = ProductPlan::where('plan_id', $plan->id)->whereNotIn('id', $idsProductPlan)->get();
                        foreach ($rowsProductPlan as $productPlanD) {
                            $productPlanD->forceDelete();
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

    public function updateInformations(PlanUpdateInformationsRequest $request, $id)
    {
        try {
            $planModel    = new Plan();

            $requestData = $request->validated();
            $price = number_format(intval(preg_replace("/[^0-9]/", "", $requestData['price'])) / 100, 2, ',', '.');
            $price = $this->getValue($price);

            $planId   = current(Hashids::decode($id));
            $plan = $planModel->with('plansSales')->where('id', $planId)->first();
            $plan->update([
                'name'        => FoxUtils::removeSpecialChars($requestData['name']),
                'description' => FoxUtils::removeSpecialChars($requestData['description']),
                'code'        => $id,
                'price'       => $price,
                'status'      => $planModel->present()->getStatus('active'),
            ]);

            $planResource = $planModel->with(['productsPlans.product'])->find($planId);

            return response()->json([
                'message' => 'Informações do plano atualizadas com sucesso',
                'plan' => new PlansDetailsResource($planResource)
            ], 200);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos dados do plano (PlansController - updateInformations)');
            report($e);

            return response()->json([
                'message' => 'Erro ao atualizar informações do plano',
            ], 400);
        }
    }

    public function updateProducts(PlanUpdateProductsRequest $request, $id)
    {
        try {
            $planModel    = new Plan();
            $productPlan  = new ProductPlan();

            $requestData = $request->validated();

            $planId   = current(Hashids::decode($id));
            $plan = $planModel->with(['productsPlans'])->where('id', $planId)->first();
            if (!empty($plan)) {
                if (count($plan->productsPlans) > 0) {
                    foreach ($plan->productsPlans as $productPlan) {
                        $productPlan->forceDelete();
                    }
                }

                foreach ($requestData['products'] as $product) {
                    $productPlan->create([
                        'product_id'         => current(Hashids::decode($product['id'])),
                        'plan_id'            => $plan->id,
                        'amount'             => $product['amount'] ?? 1,
                        'cost'               => $product['value'] ? preg_replace("/[^0-9]/", "", $product['value']) : 0,
                        'currency_type_enum' => $productPlan->present()->getCurrency($product['currency_type_enum']),
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Ocorreu um erro, plano não encontrado',
                ], 400);
            }

            return response()->json([
                'message' => 'Produtos do plano atualizados com sucesso',
            ], 200);
        } catch (Exception $e) {
            Log::warning('Erro ao tentar fazer update dos produtos do plano (PlansController - updateInformations)');
            Log::warning($e->getMessage());
            report($e);

            return response()->json([
                'message' => 'Erro ao atualizar produtos do plano',
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
                    $plan = $planModel->with(['productsPlans', 'plansSales', 'project', 'affiliateLinks'])
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
                        if (count($plan->affiliateLinks) > 0) {
                            foreach ($plan->affiliateLinks as $affiliateLink) {
                                $affiliateLink->delete();
                            }
                        }
                        $planDeleted = $plan->delete();

                        PlanService::forgetCache($planId);

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

    public function getPlans(Request $request)
    {
        try {
            $data = $request->all();

            $plans = Plan::query();

            $projectId = current(Hashids::decode($data['project_id']));
            if ($projectId) {
                $plans->where('project_id', $projectId);
            } else {
                $userId = auth()->user()->account_owner_id;
                $projects = UserProject::where('user_id', $userId)->pluck('project_id');
                $plans->whereIn('project_id', $projects);
            }

            if (!empty($data['search'])) {
                $plans->where('name', 'like', '%' . $data['search'] . '%');
            }

            $groupByVariants = boolval($data['variants'] ?? 1);

            if ($groupByVariants) {
                $plans->select('name',
                    DB::raw("min(id) as id"),
                    DB::raw("if(shopify_id is not null, concat(count(*), ' variantes'), group_concat(description)) as description"))
                    ->groupBy('name', 'shopify_id', DB::raw('if(shopify_id is null, id, 0)'));
            } else {
                $plans->select('id', 'name', 'description');
            }


            if (!empty($data['is_config'])) {
                $plans = $plans->orderBy('name')->take(10)->get();
            } else {
                $plans = $plans->orderBy('name')->paginate(10);
            }

            return PlansSelectResource::collection($plans);

        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados dos planos (PlansApiController - getPlans)');
            report($e);

            return response()->json([
                'message' => 'Ocorreu um erro, ao buscar dados dos planos',
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

    public function getPlanFilter(Request $request)
    {
        try {
            $project = $request->input('project');
            $plan = $request->input('plan') ?? '';

            $projectId = current(Hashids::decode($project));

            $planService = new PlanService();
            $plans = $planService->getPlansFilter($projectId, $plan);

            return PlansSelectResource::collection($plans);
        } catch (Exception $e) {
            report($e);

            //return response()->json(['message' => 'Erro ao tentar buscar plano'], 400);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateBulkCost(Request $request)
    {
        try {
            $costCurrency = $request->input('costCurrency');
            $updateCostShopify = $request->input('updateCostShopify');
            $updateAllCurrency = $request->input('updateAllCurrency');
            $projectId = current(Hashids::decode($request->input('project')));
            $cost = FoxUtils::onlyNumbers($request->input('cost'));
            $productsSelected = $request->input('products');

            $projectModel = new Project;
            $project = $projectModel->find($projectId);

            if (empty($project->notazz_configs)) {
                $configs = [
                    'cost_currency_type' => $projectModel->present()->getCurrencyCost($costCurrency),
                    'update_cost_shopify' => $updateCostShopify
                ];
            } else {
                $configs = json_decode($project->notazz_configs);
                $configs->cost_currency_type = $projectModel->present()->getCurrencyCost($costCurrency);
                $configs->update_cost_shopify = $updateCostShopify;
            }

            $project->update(['notazz_configs' => json_encode($configs)]);

            if ($updateAllCurrency == 1) {
                ProductPlan::whereIn('plan_id', $project->plans->pluck('id')->toArray())
                ->update([
                    'currency_type_enum' => $projectModel->present()->getCurrencyCost($costCurrency),
                ]);
            }

            if (!empty($productsSelected) && !empty($cost)) {
                foreach ($productsSelected as $p) {
                    $productId = current(Hashids::decode($p['id']));
                    $product = Product::find($productId);

                    if (count($product->variants) > 0) {
                        foreach ($product->variants as $pt) {
                            $pt->update([
                                'currency_type_enum' => $projectModel->present()->getCurrencyCost($costCurrency),
                                'cost' => $cost
                            ]);
                        }

                        ProductPlan::whereIn('product_id', $product->variants->pluck('id')->toArray())
                        ->update([
                            'currency_type_enum' => $projectModel->present()->getCurrencyCost($costCurrency),
                            'cost' => $cost
                        ]);
                    } else {
                        $product->currency_type_enum = $projectModel->present()->getCurrencyCost($costCurrency);
                        $product->cost = $cost;
                        $product->save();

                        ProductPlan::where('product_id', $productId)
                        ->update([
                            'currency_type_enum' => $projectModel->present()->getCurrencyCost($costCurrency),
                            'cost' => $cost
                        ]);
                    }
                }
            } else {
                return response()->json([
                    'message' => 'Configurações do projeto atualizadas com sucesso',
                ], 200);
            }

            return response()->json([
                'message' => 'Configurações atualizadas com sucesso',
            ], 200);

        } catch (Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Erro ao atualizar configurações',
            ], 400);
        }
    }

    public function updateConfigCost(Request $request)
    {
        try {
            $costCurrency = $request->input('costCurrency');
            $updateCostShopify = $request->input('updateCostShopify');
            $updateAllCurrency = $request->input('updateAllCurrency');
            $projectId = current(Hashids::decode($request->input('project')));

            $projectModel = new Project;
            $project = $projectModel->find($projectId);
            if (empty($project->notazz_configs)) {
                $configs = [
                    'cost_currency_type' => $projectModel->present()->getCurrencyCost($costCurrency),
                    'update_cost_shopify' => $updateCostShopify
                ];
            } else {
                $configs = json_decode($project->notazz_configs);
                $configs->cost_currency_type = $projectModel->present()->getCurrencyCost($costCurrency);
                $configs->update_cost_shopify = $updateCostShopify;
            }

            $project->update(['notazz_configs' => json_encode($configs)]);

            if ($updateAllCurrency) {
                $plans = Plan::where('project_id', $projectId)->get()->pluck('id');
                $productPlans = ProductPlan::whereIn('plan_id', $plans)->get();
                foreach ($productPlans as $productPlan) {
                    $productPlan->update(['currency_type_enum' => $projectModel->present()->getCurrencyCost($costCurrency)]);
                }
            }

            return response()->json([
                'message' => 'Configurações atualizadas com sucesso',
            ], 200);

        } catch (Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Erro ao atualizar Configurações',
            ], 400);
        }
    }

    public function saveConfigCustomProducts(Request $request)
    {
        set_time_limit(0);
        $rules = [
            'plan' => 'required',
        ];

        $messages = [
            'plan.required' => 'Informe o plano',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator, 400);
        }

        $planId = current(Hashids::decode($request->plan));
        $plan = Plan::find($planId);

        $allow_change_in_block = false;
        if (!empty($request->allow_change_in_block) && boolval($request->allow_change_in_block) === true) {
            $allow_change_in_block = true;
        }

        if (count($request->input('productsPlan', [])) > 0) {
            $productsPlanIds = array_unique($request->productsPlan);

            $details = [];
            foreach ($productsPlanIds as $productPlanId) {
                $details[$productPlanId]['type'] = !empty($request->type[$productPlanId]) ? $request->type[$productPlanId] : [];
                $details[$productPlanId]['label'] = !empty($request->label[$productPlanId]) ? $request->label[$productPlanId] : [];
            }

            $itens = [];
            foreach ($details as $productPlanId => $detailL1) {
                foreach ($detailL1 as $key2 => $detailL2) {
                    foreach ($detailL2 as $key3 => $detailL3) {
                        $itens[$productPlanId][$key3][$key2] = $detailL3 ?? '';
                    }
                }
            }

            //atualizando personalização existente
            $idsProductPlans = [];
            foreach ($itens as $productPlanId => $config) {
                $productPlan = ProductPlan::where('id', current(Hashids::decode($productPlanId)))->where('plan_id', $planId)->first();
                if (!empty($productPlan)) {
                    $productPlan->custom_config = $config;
                    $productPlan->is_custom = !empty($request->is_custom[$productPlanId]) ? 1 : 0;
                    $productPlan->update();
                    if ($allow_change_in_block === true) {
                        $this->updateAllConfigCustomProduct($plan->shopify_id, $config, !empty($request->is_custom[$productPlanId]) ? 1 : 0);
                    }
                    $idsProductPlans[] = $productPlan->id;
                }
            }

            //atualizando personalização eliminada
            $productPlans = ProductPlan::where('plan_id', $planId)->whereNotIn('id', $idsProductPlans)->get();
            foreach ($productPlans as $productPlan) {
                $productPlan->custom_config = [];
                $productPlan->is_custom = !empty($request->is_custom[$productPlan->id]) ? 1 : 0;
                $productPlan->update();
                if ($allow_change_in_block === true) {
                    $this->updateAllConfigCustomProduct($plan->shopify_id, [], !empty($request->is_custom[$productPlanId]) ? 1 : 0);
                }
            }

        } else {
            $productPlans = ProductPlan::where('plan_id', $planId)->get();
            foreach ($productPlans as $productPlan) {
                $productPlan->custom_config = [];
                $productPlan->is_custom = !empty($request->is_custom[$productPlan->id]) ? 1 : 0;
                $productPlan->update();
            }

            if ($allow_change_in_block === true) {
                $this->updateAllConfigCustomProduct($plan->shopify_id, [], !empty($request->is_custom[$productPlan->id]) ? 1 : 0);
            }
        }

        return response()->json([
            'message' => 'Configurações atualizadas com sucesso',
        ], 200);
    }

    private function updateAllConfigCustomProduct($shopify_id, $config, $is_custom)
    {
        if (!empty($shopify_id)) {
            $planIds = Plan::select('id')->where('shopify_id', $shopify_id)->get();
            foreach ($planIds as $plan) {
                $productPlans = ProductPlan::where('plan_id', $plan->id)->get();
                foreach ($productPlans as $productPlan) {
                    $productPlan->custom_config = $config;
                    $productPlan->is_custom = $is_custom;
                    $productPlan->update();
                }
            }
        }
    }
}
