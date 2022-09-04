<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Project;
use Modules\ProjectNotification\Transformers\ProjectNotificationResource;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Entities\User;
use Modules\ProjectNotification\Http\Controllers\ProjectNotificationApiController;
use Vinkla\Hashids\Facades\Hashids;

class ProjectNotificationApiDemoController extends ProjectNotificationApiController
{
    public function index($projectId)
    {
        try {
            $projectId = current(Hashids::decode($projectId));

            $projectNotifications = ProjectNotification::where('project_id', $projectId)
            ->whereHas('userProject', function($q) {
                $q->where('user_id', User::DEMO_ID);
            })
            ->paginate(5);

            return ProjectNotificationResource::collection($projectNotifications);

        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }

    public function show($projectId, $id)
    {
        try {
            if (isset($projectId) && isset($id)) {
                $projectNotificationModel = new ProjectNotification();
                $projectModel             = new Project();
                $projectNotification      = $projectNotificationModel->find(current(Hashids::decode($id)));
                $project                  = $projectModel->find(current(Hashids::decode($projectId)));

                $projectNotification->project_image   = $project->photo;
                $projectNotification->project_name    = $project->name;
                
                if ($projectNotification) {
                    return new ProjectNotificationResource($projectNotification);
                } else {
                    return response()->json(['message' => 'Erro ao buscar notificação'], 400);
                }                
            }

            return response()->json(['message' => 'Erro ao buscar Notificação'], 400);
            
        } catch (Exception $e) {
            Log::warning('Erro ao buscar dados da notificação (ProjectNotificationApiController - show)');
            report($e);

            return response()->json(['message' => 'Erro ao buscar Notificação'], 400);
        }
    }
}
