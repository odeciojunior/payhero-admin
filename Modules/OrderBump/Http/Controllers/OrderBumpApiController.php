<?php

namespace Modules\OrderBump\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\OrderBumpRule;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Shipping;
use Modules\Core\Services\CacheService;
use Modules\OrderBump\Http\Requests\OrderBumpRequest;
use Modules\OrderBump\Transformers\OrderBumpResource;
use Modules\OrderBump\Transformers\OrderBumpShowResource;

class OrderBumpApiController extends Controller
{

    public function index(Request $request)
    {
        try {
            $data = $request->all();

            $projectId = hashids_decode($data['project_id']);

            $rules = OrderBumpRule::where('project_id', $projectId)
                ->orderByDesc('id')
                ->paginate(5);

            return OrderBumpResource::collection($rules);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao listar regras de order bump!'], 400);
        }
    }

    public function store(OrderBumpRequest $request)
    {
        try {
            $data = $request->getData();

            OrderBumpRule::create($data);

            CacheService::forgetContainsUnique(CacheService::CHECKOUT_OB_RULES, $data['project_id']);

            return response()->json(['message' => 'Nova regra de order bump criada com succeso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao salvar nova regra de order bump!'], 400);
        }
    }

    public function show($id)
    {
        try {
            $id = hashids_decode($id);
            $rule = OrderBumpRule::find($id);

            $selectPlans = ['id', 'name', 'description'];
            if ($rule->use_variants) {
                $rawVariants = DB::raw('(select sum(if(p.shopify_id is not null and p.shopify_id = plans.shopify_id, 1, 0)) from plans p) as variants');
                $selectPlans[] = $rawVariants;
            }

            if ($rule->apply_on_shipping[0] === 'all') {
                $rule->apply_on_shipping = collect()->push((object)[
                    'id' => 'all',
                    'name' => 'Qualquer frete',
                    'information' => '',
                ]);
            } else {
                $rule->apply_on_shipping = Shipping::select('id', 'name', 'information')
                    ->whereIn('id', $rule->apply_on_shipping)
                    ->get();
            }

            if ($rule->apply_on_plans[0] === 'all') {
                $rule->apply_on_plans = collect()->push((object)[
                    'id' => 'all',
                    'name' => 'Qualquer plano',
                    'description' => '',
                    'variants' => 0,
                ]);
            } else {
                $rule->apply_on_plans = Plan::select($selectPlans)
                    ->whereIn('id', $rule->apply_on_plans)
                    ->get();
            }
            $rule->offer_plans = Plan::select($selectPlans)
                ->whereIn('id', $rule->offer_plans)
                ->get();

            return new OrderBumpShowResource($rule);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao exibir detalhes da regra de order bump!'], 400);
        }
    }

    public function update($id, OrderBumpRequest $request)
    {
        try {
            $data = $request->getData();
            $id = hashids_decode($id);

            $rule = OrderBumpRule::find($id);
            $rule->update($data);

            CacheService::forgetContainsUnique(CacheService::CHECKOUT_OB_RULES, $rule->project_id);
            CacheService::forget(CacheService::CHECKOUT_OB_RULE_PLANS, $rule->id);
            CacheService::forgetContainsUnique(CacheService::SHIPPING_OB_RULES, $rule->id);

            return response()->json(['message' => 'Regra order bump atualizada com succeso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar regra de order bump!'], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $id = hashids_decode($id);

            $rule = OrderBumpRule::find($id);
            $projectId = $rule->project_id;
            $rule->delete();

            CacheService::forgetContainsUnique(CacheService::CHECKOUT_OB_RULES, $projectId);
            CacheService::forget(CacheService::CHECKOUT_OB_RULE_PLANS, $id);
            CacheService::forgetContainsUnique(CacheService::SHIPPING_OB_RULES, $id);

            return response()->json(['message' => 'Regra order bump excluída com succeso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir regra de order bump!'], 400);
        }
    }
}
