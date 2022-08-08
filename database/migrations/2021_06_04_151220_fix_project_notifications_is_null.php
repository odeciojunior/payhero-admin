<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ProjectNotification;
use Modules\Core\Services\ProjectNotificationService;

class FixProjectNotificationsIsNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projectNotificationService = new ProjectNotificationService();

        $projects = Project::leftJoin("project_notifications", "project_notifications.project_id", "projects.id")
            ->whereNull("project_notifications.id")
            ->get("projects.id");

        foreach ($projects as $project) {
            $projectNotificationService->createProjectNotificationDefault($project->id);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
