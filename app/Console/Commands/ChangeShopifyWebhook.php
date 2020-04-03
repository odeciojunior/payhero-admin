<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Project;
use Modules\Core\Services\ShopifyService;

class ChangeShopifyWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChangeShopifyWebhook';

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
        $projectsModel = new Project();

        $projects = $projectsModel->whereHas('shopifyIntegrations')
            ->orderByDesc('id')
            ->get();
        $total = $projects->count();

        foreach ($projects as $key => $project) {
            $count = $key + 1;
            $this->info("Projeto {$count} de {$total}: {$project->name}");

            foreach ($project->shopifyIntegrations as $integration){

            }

            $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);

        }
    }
}
