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

    private function doRequest(string $uri = "/", array $data = null, string $method = "GET", array $headers = [])
    {
        $curl = curl_init();

        $url = 'https://sentry.io/api/0' . $uri;
        $defaultHeaders = [
            'Authorization: Bearer b15fcc11d79140a788254bd50c27f3013b72b677670444df9dcc34239262b2a0'
        ];

        $method = strtoupper($method);

        if ($method !== "GET") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                $data = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        } elseif (!empty($data)) {
            $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $defaultHeaders + $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result);
    }

    public function handle()
    {
        $projects = Project::with(['shopifyIntegrations', 'users'])
            ->whereIn('id', [
                5394,
                5658,
                5706,
                5729,
                5737,
                5845,
                5875,
                5899,
                6050,
                6064,
                6069,
                6140,
                6174,
                6186,
                6192,
                6239,
                6266,
                6290,
                6301,
            ])
            ->get();

        $bar = $this->getOutput()->createProgressBar($projects->count());
        $bar->start();

        foreach ($projects as $project) {
            $integration = $project->shopifyIntegrations->first();
            if (empty($integration)) {
                continue;
            }
            $user = $project->users->first();

            ImportShopifyProductsStore::dispatch($integration, $user->id);

            $bar->advance();
        }
        $bar->finish();

        return;

        /*
        $ids = cache()->remember('ids', 1800, function () {
            $ids = [];
            $count = 0;

            $events = $this->doRequest('/issues/2795578968/events/', ['full' => true, 'cursor' => '0:' . ($count * 100) . ':0']);
            while (!empty($events)) {
                foreach ($events as $event) {
                    $id = $event->entries[2]->data->data->variant_id_1 ?? null;
                    $ids[$id] = $id;
                }
                $count++;
                sleep(1);
                $events = $this->doRequest('/issues/2795578968/events/', ['full' => true, 'cursor' => '0:' . ($count * 100) . ':0']);
            }

            return $ids;
        });

        $string = implode("', '", $ids);

        $services = [];
        $products = Product::with('project.shopifyIntegrations')
            ->whereIn('shopify_variant_id', $ids)
            ->orderByDesc('id')
            ->get();

        foreach ($products as $product) {
            $project = $product->project;
            $services[$project->id] = $project;
            if (empty($services[$project->id])) {
                $integration = $project->shopifyIntegrations->first();
                if (empty($integration)) {
                    continue;
                }
                $services[$project->id] = new ShopifyService($integration->url_store, $integration->token, false);
            }
            $service = $services[$project->id];

            $shopifyProduct = $service->getProductVariant($product->shopify_variant_id);

            if($shopifyProduct->getTitle() === $product->name){

            }
        }

        dd(array_keys($services));
        */
    }
}
