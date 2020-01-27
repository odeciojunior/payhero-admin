<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Project;

class CreateProjectNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateProjectNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            $projectModel = new Project();

            $projects = $projectModel->doesnthave('notifications')->get();

            $projectNotificationService = new ProjectNotificationService();

            foreach ($projects as $project) {
                $projectNotificationService->createProjectNotificationDefault($project->id);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


}
