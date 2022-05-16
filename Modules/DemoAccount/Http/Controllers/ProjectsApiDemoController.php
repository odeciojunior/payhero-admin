<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectService;
use Spatie\Activitylog\Models\Activity;
use Modules\Projects\Http\Controllers\ProjectsApiController;
use Modules\Projects\Transformers\ProjectsResource;

class ProjectsApiDemoController extends ProjectsApiController
{
    public function index(Request $request)
    {
        try {
                        
            $projects = Project::leftJoin('users_projects', 'projects.id', '=', 'users_projects.project_id')
            ->select('projects.*', 'users_projects.order_priority as order_p')
            ->whereIn('projects.status', Project::STATUS_ACTIVE)
            ->where('users_projects.user_id', Company::USER_ID_DEMO)
            ->whereNull('users_projects.deleted_at')
            ->orderBy('projects.status')
            ->orderBy('order_p')
            ->orderBy('projects.id', 'DESC');

            return ProjectsResource::collection($projects->with('domains')->get());
            
        } catch (Exception $e) {
            report($e);

            return response()->json(['message' => 'Erro ao tentar acessar projetos'], 400);
        }
    }
}