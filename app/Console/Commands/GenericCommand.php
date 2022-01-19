<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Sale;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {

        $projects = Project::leftJoin('users_projects', 'projects.id', '=', 'users_projects.project_id')
        ->select('projects.*', 'users_projects.order_priority as order_p')
        ->where('users_projects.user_id', 6191)
        ->whereNull('users_projects.deleted_at')
        ->orderBy('projects.status')
        ->orderBy('order_p')
        ->orderBy('projects.id', 'DESC')->get();

dd(count($projects));

        $apiSale = Sale::where('owner_id', 6191)->exists();
        dd(empty($apiSale));
    }
    
}
