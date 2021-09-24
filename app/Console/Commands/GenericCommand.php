<?php

namespace App\Console\Commands;

use App\Jobs\ImportShopifyTrackingCodesJob;
use Illuminate\Console\Command;
use Modules\Core\Entities\Project;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $project = Project::find(3722);
        ImportShopifyTrackingCodesJob::dispatch($project, false);
    }
}
