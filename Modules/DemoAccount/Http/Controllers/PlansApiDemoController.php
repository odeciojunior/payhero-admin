<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\ProductPlan;
use Modules\Plans\Transformers\PlansResource;
use Modules\Plans\Http\Controllers\PlansApiController;

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
}
