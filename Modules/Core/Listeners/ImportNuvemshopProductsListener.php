<?php

namespace Modules\Core\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\NuvemshopIntegration;
use Modules\Core\Events\ImportNuvemshopProductsEvent;
use Modules\Core\Services\Nuvemshop\NuvemshopAPI;
use Modules\Core\Services\Nuvemshop\NuvemshopService;

class ImportNuvemshopProductsListener implements ShouldQueue
{
    public $queue = "long";

    private NuvemshopIntegration $integration;
    private NuvemshopAPI $api;
    private NuvemshopService $service;

    public function handle(ImportNuvemshopProductsEvent $event)
    {
        $this->integration = $event->integration;
        $this->api = new NuvemshopAPI($this->integration->store_id, $this->integration->token);
        $this->service = new NuvemshopService($this->integration);

        $page = 1;
        $perPage = 100;
        $hasNextPage = true;

        while ($hasNextPage) {
            $products = $this->api->findAllProducts(["page" => $page, "per_page" => $perPage]);

            foreach ($products as $product) {
                $this->service->createProduct($product);
            }

            if (count($products) < $perPage) {
                $hasNextPage = false;
                continue;
            }

            $page++;
            sleep(1);
        }

        $this->service->createWebhooks();
    }
}
