<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\NuvemshopIntegration;
use Modules\Core\Entities\PostbackLog;
use Modules\Core\Entities\Project;
use Modules\Core\Services\Nuvemshop\NuvemshopAPI;
use Modules\Core\Services\Nuvemshop\NuvemshopService;

class ProcessNuvemshopPostbackJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
            PostbackLog::create([
                "origin" => 9,
                "data" => json_encode($this->postback),
                "description" => "nuvemshop",
            ]);

            $project = Project::find($this->projectId);
            if (empty($project)) {
                return;
            }

            $integration = NuvemshopIntegration::where("project_id", $project->id)->first();
            if (empty($integration)) {
                return;
            }

            $nuvemshopApi = new NuvemshopAPI($integration->store_id, $integration->token);
            $nuvemshopService = new NuvemshopService($integration);

            if ($this->postback["event"] == "product/created") {
                $product = $nuvemshopApi->findProduct($this->postback["id"]);
                $nuvemshopService->createProduct($product);
            }

            if ($this->postback["event"] == "product/updated") {
                $product = $nuvemshopApi->findProduct($this->postback["id"]);
                $nuvemshopService->updateProduct($product);
            }

            if ($this->postback["event"] == "order/fulfilled") {
                $order = $nuvemshopApi->findOrder($this->postback["id"]);
                $nuvemshopService->fulfillOrder($order);
            }
        } catch (\Exception $e) {
            report($e);
        }
    }

    public function tags()
    {
        return ["shopify:postback"];
    }
}
