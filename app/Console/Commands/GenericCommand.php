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
        try {
            $projects = Project::with('shopifyIntegrations')
                ->whereHas('shopifyIntegrations')
                ->where('status', 1)
                ->get();

            foreach ($projects as $project){
                ImportShopifyTrackingCodesJob::dispatch($project);
            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
