<?php

namespace App\Console\Commands;

use App\Jobs\ImportShopifyProductsStore;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ShopifyService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $project = Project::with(['shopifyIntegrations', 'users'])
            ->whereIn('id', [6349])
            ->first();

        $integration = $project->shopifyIntegrations->first();
        $user = $project->users->first();

        ImportShopifyProductsStore::dispatchNow($integration, $user->id);
    }
}
