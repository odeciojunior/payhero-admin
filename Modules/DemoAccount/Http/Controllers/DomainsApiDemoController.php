<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Domains\Transformers\DomainResource;
use Modules\Domains\Http\Controllers\DomainsApiController;

class DomainsApiDemoController extends DomainsApiController
{
    public function index($projectId){
        try {
            $projectId = hashids_decode($projectId);
            $project = Project::find($projectId);

            if (empty($project)) {
                return response()->json(['message' => 'Erro ao listar dados de domínios'], 400);
            }

            $domains = Domain::with('project')->where('project_id', $projectId);

            return DomainResource::collection($domains->orderBy('id', 'DESC')->paginate(5));
            
        } catch (Exception $e) {
            report($e);

            return response()->json(
                [
                    'message' => 'Erro ao listar dados de domínios',
                ],
                400
            );
        }
    }
}
