<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\ProjectUpsellRule;
use Modules\ProjectUpsellRule\Transformers\ProjectsUpsellResource;
use Modules\ProjectUpsellRule\Http\Controllers\ProjectUpsellRuleApiController;

class ProjectUpsellRuleApiDemoController extends ProjectUpsellRuleApiController
{
    public function index(Request $request)
    {
        try {
            $data = $request->all();
            
            $projectId = current(Hashids::decode($data['project_id']));
            if (empty($projectId)) {
                return  response()->json([
                    'message' => 'Erro ao listar dados de upsell',
                ], 400);
            }

            $projectUpsell = ProjectUpsellRule::where('project_id', $projectId);

            return ProjectsUpsellResource::collection($projectUpsell->paginate(5));
            
        } catch (Exception $e) {
            
            report($e);
            return response()->json(['message' => 'Erro ao listar dados de upsell'], 400);
        }
    }
}
