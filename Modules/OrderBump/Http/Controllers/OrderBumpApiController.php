<?php

namespace Modules\OrderBump\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\OrderBumpRule;
use Modules\Core\Entities\Plan;
use Modules\Core\Services\CacheService;
use Modules\OrderBump\Http\Requests\OrderBumpRequest;
use Modules\OrderBump\Transformers\OrderBumpResource;
use Modules\OrderBump\Transformers\OrderBumpShowResource;
use Vinkla\Hashids\Facades\Hashids;

class OrderBumpApiController extends Controller
{

    public function index(Request $request)
    {
        try {

            $orderBumpModel = new OrderBumpRule();

            $data = $request->all();

            $projectId = current(Hashids::decode($data['project_id']));

            $rules = $orderBumpModel->where('project_id', $projectId)
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
            $orderBumpModel = new OrderBumpRule();

            $data = $request->getData();

            $orderBumpModel->create($data);

            CacheService::forgetContainsUnique(CacheService::CHECKOUT_OB_RULES, $data['project_id']);

            return response()->json(['message' => 'Nova regra de order bump criada com succeso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao salvar nova regra de order bump!'], 400);
        }
    }

    public function show($id)
    {
        try {
            $orderBumpModel = new OrderBumpRule();
            $plansModel = new Plan();

            $id = current(Hashids::decode($id));
            $rule = $orderBumpModel->find($id);

            $rawVariants = DB::raw('(select sum(if(p.shopify_id is not null and p.shopify_id = plans.shopify_id, 1, 0)) from plans p) as variants');

            if ($rule->apply_on_plans[0] === 'all') {
                $rule->apply_on_plans = collect()->push((object)[
                    'id' => 'all',
                    'name' => 'Qualquer plano',
                    'description' => '',
                    'variants' => 0,
                ]);
            } else {
                $rule->apply_on_plans = $plansModel->select('id', 'name', 'description', $rawVariants)
                    ->whereIn('id', $rule->apply_on_plans)
                    ->get();
            }
            $rule->offer_plans = $plansModel->select('id', 'name', 'description', $rawVariants)
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
            $orderBumpModel = new OrderBumpRule();

            $data = $request->getData();
            $id = current(Hashids::decode($id));

            $rule = $orderBumpModel->find($id);
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
            $orderBumpModel = new OrderBumpRule();

            $id = current(Hashids::decode($id));

            $rule = $orderBumpModel->find($id);
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
