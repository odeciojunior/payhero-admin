<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ProjectNotificationService;
use Modules\Core\Services\ProjectService;

class CreateProjectUpsellConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CreateProjectUpsellConfig';

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

            $projects = $projectModel->doesnthave('upsellConfig')->get();
            $projectService = new ProjectService();

            foreach ($projects as $project) {
                $projectService->createUpsellConfig($project->id);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


}
