<?php

namespace Modules\Plans\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\AffiliateLink;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\PlanService;
use Modules\Plans\Http\Requests\PlanStoreRequest;
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

            $planModel    = new Plan();
            $projectModel = new Project();

            activity()->on($planModel)->tap(function(Activity $activity) {
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
                            'project.domains' => function($query) use ($projectId) {
                                $query->where('project_id', $projectId)
                                ->where('status', 3)
                                ->first();
                            },
                        ])->where('project_id', $projectId);

                        if (!empty($request->input('plan'))) {
                            $plans = $plans->where(
                                function ($query) use ($request) {
                                    $query->where('name', 'like', '%' . $request->input('plan') . '%')
                                    ->orWhere('price', 'like', '%'. str_replace(array('R', '$', ' ', '.', ','), array('', '', '', '', '.'),$request->input('plan')). '%')
                                    ->orWhere('description', 'like', '%' . $request->input('plan') . '%');
                                }
                            );
                        }

                        if($project->status == Project::STATUS_ACTIVE){
                            $plans = $plans->where('status',Plan::STATUS_ACTIVE);

                        }else{
                            $plans = $plans->where('status',Plan::STATUS_DESABLE);
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
    public function store(PlanStoreRequest $request, $projectID)
    {
        try {
            $planModel          = new Plan();
            $productPlan        = new ProductPlan();
            $projectModel       = new Project();
            $affiliateLinkModel = new AffiliateLink();
            $planService        = new PlanService();

            $requestData               = $request->validated();
            $requestData['project_id'] = current(Hashids::decode($requestData['project_id']));
            $requestData['status']     = 1;

            $projectId = $requestData['project_id'];

            if ($projectId) {
                //hash ok

                $project = $projectModel->with('affiliates', 'affiliates.user')->find($projectId);

                if (Gate::allows('edit', [$project])) {
                    $requestData['price'] = number_format(intval(preg_replace("/[^0-9]/", "", $requestData['price'])) / 100, 2, ',', '.');
                    $requestData['price'] = $this->getValue($requestData['price']);
                    if (!empty($requestData['products']) && !empty($requestData['product_amounts'])) {
                        $requestData['name']        = FoxUtils::removeSpecialChars($requestData['name']);
                        $requestData['description'] = FoxUtils::removeSpecialChars($requestData['description']);

                        $plan = $planModel->create($requestData);
                        if (!empty($plan)) {

                            $plan->update(['code' => $plan->id_code]);
                            foreach ($requestData['products'] as $keyProduct => $product) {

                                $requestData['product_cost'][$keyProduct] = preg_replace("/[^0-9]/", "", $requestData['product_cost'][$keyProduct]);
                                if (empty($requestData['product_cost'][$keyProduct])) {
                                    $requestData['product_cost'][$keyProduct] = 0;
                                }

                                $productPlan->create([
                                    'product_id'         => $requestData['products'][$keyProduct],
                                    'plan_id'            => $plan->id,
                                    'amount'             => $requestData['product_amounts'][$keyProduct] ?? 1,
                                    'cost'               => $requestData['product_cost'][$keyProduct] ?? 0,
                                    'currency_type_enum' => $productPlan->present()->getCurrency($requestData['currency'][$keyProduct]),
                                ]);
                            }

                            if (count($project->affiliates) > 0) {

                                foreach ($project->affiliates as $affiliate) {
                                    $affiliateHash = Hashids::connection('affiliate')->encode($affiliate->id);
                                    $affiliateLinkModel->create([
                                        'affiliate_id'  => $affiliate->id,
                                        'plan_id'       => $plan->id,
                                        'parameter'     => $affiliateHash . Hashids::connection('affiliate')->encode($plan->id),
                                        'clicks_amount' => 0,
                                        'link'          => $planService->getCheckoutLink($plan),
                                    ]);
                                }
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
            $planModel    = new Plan();
            $projectModel = new Project();

            activity()->on($planModel)->tap(function(Activity $activity) use ($id) {
                $activity->log_name   = 'visualization';
                $activity->subject_id = current(Hashids::decode($id));
            })->log('Visualizou tela detalhes do plano');

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

                    $plan = $planModel->with('plansSales')->where('id', $planId)->first();

                    $plan->update([
                        'name'        => FoxUtils::removeSpecialChars($requestData['name']),
                        'description' => FoxUtils::removeSpecialChars($requestData['description']),
                        'code'        => $id,
                        'price'       => $requestData["price"],
                        'status'      => $planModel->present()->getStatus('active'),
                    ]);

                    $productPlans      = $productPlan->where('plan_id', $plan->id)->get();
                    $productPlanId     = $productPlans->pluck('product_id')->toArray();
                    $productPlanAmount = $productPlans->pluck('amount')->toArray();
                    $resultId          = array_diff($productPlanId, $requestData['products']);
                    $resultAmount      = array_diff($productPlanAmount, $requestData['product_amounts']);

                    if (count($plan->plansSales) > 0 && (!empty($resultId) || !empty($resultAmount)) || (count($requestData['products']) > count($productPlanId))) {
                        return response()->json(['message' => 'Impossível editar os produtos do plano pois possui vendas associadas.'], 400);
                    }

                    // if (count($productPlans) > 0) {
                    //     foreach ($productPlans as $productPlan) {

                    //         $productPlan->forceDelete();
                    //     }
                    // }

                    $idsProductPlan = [];
                    $objProductPlan = new ProductPlan();
                    if (!empty($requestData['products']) && !empty($requestData['product_amounts'])) {
                        foreach ($requestData['products'] as $keyProduct => $product) {
                            if (empty($requestData['product_cost'][$keyProduct]) || $requestData['product_cost'][$keyProduct] == '0.00'){
                                $requestData['product_cost'][$keyProduct] = 0;
                            }else{
                                $requestData['product_cost'][$keyProduct] = preg_replace("/[^0-9]/", "", $requestData['product_cost'][$keyProduct]);
                            }

                            $productPlan = ProductPlan::where('product_id',$requestData['products'][$keyProduct])->where('plan_id',$plan->id)->first();
                            if(!empty($productPlan)){
                                $productPlan->amount = $requestData['product_amounts'][$keyProduct] ?? 1;
                                $productPlan->cost = $requestData['product_cost'][$keyProduct] ?? 0;
                                $productPlan->currency_type_enum = $productPlan->present()->getCurrency($requestData['currency'][$keyProduct]);
                                $productPlan->update();
                                $idsProductPlan[] = $productPlan->id;                                
                            }else{                               
                                $productPlan = ProductPlan::create([
                                    'product_id'         => $requestData['products'][$keyProduct],
                                    'plan_id'            => $plan->id,
                                    'amount'             => $requestData['product_amounts'][$keyProduct] ?? 1,
                                    'cost'               => $requestData['product_cost'][$keyProduct] ?? 0,
                                    'currency_type_enum' => $objProductPlan->present()
                                                            ->getCurrency($requestData['currency'][$keyProduct]),
                                ]);
                                $idsProductPlan[] = $productPlan->id;                                
                            }
                        }
                    }

                    if(count($idsProductPlan)>0){
                        $rowsProductPlan = ProductPlan::where('plan_id',$plan->id)->whereNotIn('id',$idsProductPlan)->get();
                        foreach($rowsProductPlan as $productPlanD){
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
                    $plan    = $planModel->with(['productsPlans', 'plansSales', 'project', 'affiliateLinks'])
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
            $data      = $request->all();
            $planModel = new Plan();
            $projectId = current(Hashids::decode($data['project_id']));
            if ($projectId) {

                $plans = $planModel->where('project_id', $projectId);

                if (!empty($data['search'])) {
                    $plans->where('name', 'like', '%' . $data['search'] . '%');
                }

                $groupByVariants = boolval($data['variants'] ?? 1);

                if($groupByVariants){
                    $plans->select('name',
                        DB::raw("if(shopify_id is not null,(select p.id from plans p where p.shopify_id = plans.shopify_id and p.name = plans.name and p.deleted_at is null limit 1), group_concat(id)) as id"),
                        DB::raw("if(shopify_id is not null, concat(count(*), ' variantes'), group_concat(description)) as description"))
                        ->groupBy('name', 'shopify_id', DB::raw('if(shopify_id is null, id, 0)'));
                } else {
                    $plans->select('id', 'name', 'description');
                }

                $plans = $plans->orderBy('name')->paginate(10);

                return PlansSelectResource::collection($plans);
            } else {
                return response()->json([
                    'message' => 'Ocorreu um erro, ao buscar dados dos planos',
                ], 400);
            }
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

    public function updateBulkCost(Request $request)
    {
        try {
            $cost = FoxUtils::onlyNumbers($request->input('cost'));
            $planId = current(Hashids::decode($request->input('plan')));
            $plan = Plan::find($planId);
            $planIds = Plan::where('name', $plan->name)
            ->where('shopify_id', $plan->shopify_id)
            ->get()->pluck('id');
            $productPlans = ProductPlan::whereIn('plan_id', $planIds)->get();

            foreach ($productPlans as $productPlan) {
                $productPlan->update(['cost' => $cost]);
            }

            return response()->json([
                'message' => 'Custo atualizado com sucesso',
            ], 200);

        } catch (Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Erro ao atualizar custo do plano',
            ], 400);
        }
    }

    public function updateConfigCost(Request $request)
    {
        try {
            $costCurrency      = $request->input('costCurrency');
            $updateCostShopify = $request->input('updateCostShopify');
            $updateAllCurrency = $request->input('updateAllCurrency');
            $projectId         = current(Hashids::decode($request->input('project')));

            $projectModel = new Project;
            $project = $projectModel->find($projectId);
            if(empty($project->notazz_configs)) {
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

            if($updateAllCurrency) {
                $plans = Plan::where('project_id', $projectId)->get()->pluck('id');
                $productPlans = ProductPlan::whereIn('plan_id', $plans)->get();
                foreach ($productPlans as $productPlan) {
                    dd($productPlan);
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

    public function saveConfigCustomProducts(Request $request){
        
        $rules = [
            'plan'=>'required',            
        ];
        $messages = [
            'plan.required'=>'Informe o plano',            
        ];

        $validator = Validator::make($request->all(),$rules,$messages);

        if($validator->fails()){
            return response()->json($validator, 400);
        }

        $planId = current(Hashids::decode($request->plan));
        $plan = Plan::find($planId);

        $allow_change_in_block = false;
        if(!empty($request->allow_change_in_block) && boolval($request->allow_change_in_block) === true){
            $allow_change_in_block = true;
        }

        if(count($request->input('productsPlan',[]))>0){
            $productsPlanIds = array_unique($request->productsPlan);                   
            
            $details = [];        
            foreach($productsPlanIds as $productPlanId){            
                $details[$productPlanId]['type'] = !empty($request->type[$productPlanId]) ? $request->type[$productPlanId]: []; 
                $details[$productPlanId]['label'] = !empty($request->label[$productPlanId]) ? $request->label[$productPlanId]: []; 
            }
    
            $itens = [];
            foreach($details as $productPlanId=>$detailL1){
               foreach($detailL1 as $key2=> $detailL2){
                    foreach($detailL2 as $key3=>$detailL3){
                        $itens[$productPlanId][$key3][$key2] = $detailL3??''; 
                    }
               }
            }

            foreach($itens as $productPlanId=>$config)
            {                
                $productPlan = ProductPlan::where('id',$productPlanId)->where('plan_id',$planId)->first();
                if(!empty($productPlan))
                {
                    $productPlan->custom_config = $config;
                    $productPlan->is_custom = !empty($request->is_custom[$productPlanId]) ? 1:0;  
                    $productPlan->update();
                    if($allow_change_in_block===true){
                        $this->updateAllConfigCustomProduct($plan->shopify_id,$config,!empty($request->is_custom[$productPlanId]) ? 1:0);
                    }
                }
            }
        }else{            
            $productPlans = ProductPlan::where('plan_id', $planId)->get();    
            foreach ($productPlans as $productPlan) {
                $productPlan->custom_config = [];
                $productPlan->is_custom = !empty($request->is_custom[$productPlan->id]) ? 1:0;
                $productPlan->update();
            }

            if($allow_change_in_block===true){
                $this->updateAllConfigCustomProduct($plan->shopify_id,[],!empty($request->is_custom[$productPlan->id]) ? 1:0);
            }
        }
               
        return response()->json([
            'message' => 'Configurações atualizadas com sucesso',
        ], 200);
    }

    private function updateAllConfigCustomProduct($shopify_id,$config,$is_custom)
    { 
        if(!empty($shopify_id)){
            $planIds = Plan::where('shopify_id', $shopify_id)->get()->pluck('id');
    
            $productPlans = ProductPlan::whereIn('plan_id', $planIds)->get();
            
            foreach ($productPlans as $productPlan) {
                $productPlan->custom_config = $config;
                $productPlan->is_custom = $is_custom;
                $productPlan->update();
            }
        }
    }
}
