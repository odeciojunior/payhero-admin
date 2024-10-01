<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;

class ProcessWooCommerceOrderNotes implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $projectId;
    private $data;

    /**
     * Create a new job instance.
     */
    public function __construct(int $projectId, array $data)
    {
        $this->projectId = $projectId;
        $this->data = $data;
    }

    public function handle()
    {
        try {
            $integration = WooCommerceIntegration::where("project_id", $this->projectId)->first();

            $service = new WooCommerceService(
                $integration->url_store,
                $integration->token_user,
                $integration->token_pass
            );

            $notes = $service->woocommerce->get("orders/" . $this->data["id"] . "/notes");

            foreach ($notes as $note) {
                $trackingStr = "Tracking number: ";

                if (strpos($note->note, $trackingStr)) {
                    $code = substr($note->note, strpos($note->note, $trackingStr) + strlen($trackingStr));
                    $code = substr($code, 0, strpos($code, "\n"));

                    $this->data["correios_tracking_code"] = $code;

                    ProcessWooCommercePostbackTracking::dispatch($this->projectId, $this->data);
                }
            }

            // Log::debug($notes);
        } catch (Exception $e) {
            //
        }
    }
}
