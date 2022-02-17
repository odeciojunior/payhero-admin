<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\ShopifyIntegration;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ShopifyService;

class ProcessShopifyPostbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $projectId;
    private array $postback;

    public function __construct(int $projectId, array $postback)
    {
        $this->projectId = $projectId;
        $this->postback = $postback;
    }

    public function handle()
    {
        try {
            $userProject = UserProject::with([
                'user',
                'project'
            ])->where('project_id', $this->projectId)
                ->where('type_enum', UserProject::TYPE_PRODUCER_ENUM)
                ->first();

            if (!empty($userProject)) {

                $user = $userProject->user;
                $project = $userProject->project;
                $postback = $this->postback;

                $integration = ShopifyIntegration::where('project_id', $project->id)->first();

                if (!empty($user) && !empty($project) && !empty($integration)) {
                    $shopifyService = new ShopifyService($integration->url_store, $integration->token, false);

                    if (!empty($postback['variants']) && count($postback['variants']) > 0) {
                        $variant = current($postback['variants']);
                    }

                    if (empty($variant['product_id'])) {
                        $variant['product_id'] = $postback['id'];
                    }

                    $shopifyService->importShopifyProduct($project->id, $user->id, $variant['product_id']);
                }
            }
        } catch (\Exception $e) {
            report($e);
        }
    }

    public function tags()
    {
        return ['shopify:postback'];
    }
}
