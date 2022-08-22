<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Google\Service\AnalyticsReporting\Activity;
use Illuminate\Http\Request;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\ProductPlan;
use Modules\Plans\Transformers\PlansResource;
use Modules\Plans\Http\Controllers\PlansApiController;
use Modules\Plans\Transformers\PlansDetailsResource;

class PlansApiDemoController extends PlansApiController
{
    public function index($projectId, Request $request)
    {
        try {
            
            if (empty($projectId)) {
                return response()->json([
                    'message' => 'Projeto não encontrado',
                ], 400);
            }

            $projectId = current(Hashids::decode($projectId));

            if (empty($projectId)) {
                return response()->json([
                    'message' => 'Projeto não encontrado',
                ], 400);
            }

            $project = Project::find($projectId);
            
            $plans = Plan::with([
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

            $plans = $plans->where('status', $project->status == Project::STATUS_ACTIVE ? Plan::STATUS_ACTIVE:Plan::STATUS_DESABLE);

            $plans = $plans->orderBy('id', 'DESC')->paginate(5);

            return PlansResource::collection($plans);
                            
        } catch (Exception $e) {
            
            report($e);

            return response()->json([
                'message' => 'Erro ao tentar listar planos',
            ], 400);
        }
    }

    public function show($projectID, $id)
    {
        try {
            $projectId = current(Hashids::decode($projectID));

            if (empty($projectId)) {                
                return response()->json(['message' => 'error'], 200);
            }

            if (!empty($id)) {
                $planId = current(Hashids::decode($id));

                $plan = Plan::with([
                    'productsPlans' => function ($query) use ($planId) {
                        $query->where('plan_id', $planId);
                    },
                    'productsPlans.product',
                    'project.domains' => function ($query) use ($projectId) {
                        $query->where([['project_id', $projectId], ['status', 3]])->first();
                    },
                ])->find($planId);

                if (empty($plan)) {
                    return response()->json(['message' => 'error',], 200);
                } 

                return new PlansDetailsResource($plan);                
            } 

            return response()->json(['message' => 'error'], 200);                
                            
            
        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao buscar dados do plano!',
            ], 400);
        }
    }
}
