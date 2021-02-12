<?php

namespace Modules\OrderBump\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Entities\OrderBumpRule;
use Modules\Core\Entities\Plan;
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

            $rule = $orderBumpModel->create($data);

            if (!empty($rule) && $data['active_flag']) {
                $orderBumpModel->where('project_id', $data['project_id'])
                    ->where('id', '!=', $rule->id)
                    ->update([
                        'active_flag' => false,
                    ]);
            }

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

            if ($rule->apply_on_plans[0] === 'all') {
                $rule->apply_on_plans = collect()->push((object)[
                    'id' => 'all',
                    'name' => 'Qualquer plano',
                    'description' => null,
                ]);
            } else {
                $rule->apply_on_plans = $plansModel->select('id', 'name', 'description')
                    ->whereIn('id', $rule->apply_on_plans)
                    ->get();
            }
            $rule->offer_plans = $plansModel->select('id', 'name', 'description')
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

            $rule = $orderBumpModel->find($id)
                ->update($data);

            if (!empty($rule) && $data['active_flag']) {
                $orderBumpModel->where('project_id', $data['project_id'])
                    ->where('id', '!=', $id)
                    ->update([
                        'active_flag' => false,
                    ]);
            }

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

            $orderBumpModel->find($id)
                ->delete();

            return response()->json(['message' => 'Regra order bump excluída com succeso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao excluir regra de order bump!'], 400);
        }
    }
}
