<?php

namespace Modules\DemoAccount\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Entities\Company;
use Modules\ProjectNotification\Transformers\ProjectNotificationResource;
use Modules\Core\Entities\ProjectNotification;
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
                $q->where('user_id', Company::USER_ID_DEMO);
            })
            ->paginate(5);

            return ProjectNotificationResource::collection($projectNotifications);

        } catch (Exception $e) {
            return response()->json(['message' => 'Ocorreu algum erro'], 400);
        }
    }
}
