<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Services\TrackingService;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $productsQuery = Product::with([
            'productsPlans.plan'
        ])->select('id', 'project_id', 'shopify_id')
            ->where('shopify', 0)
            ->whereNotNull('shopify_id')
            ->where('shopify_id', '!=', '');

        $bar = $this->getOutput()->createProgressBar();
        $bar->start($productsQuery->count());

        $productsQuery->chunk(1000, function ($products) use ($bar) {
            foreach ($products as $product) {

                try {

                    if (!stristr($product->shopify_id, '-')) {
                        $plan = null;
                        foreach ($product->productsPlans as $pp) {
                            if ($pp->plan->shopify_id === $product->shopify_id) {
                                $plan = $pp->plan;
                                break;
                            }
                        }

                        $newId = $product->shopify_id . '-' . hashids_encode($product->project_id);
                        $product->shopify_id = $plan->shopify_id = $newId;

                        $product->save();
                        $plan->save();
                    } else {
                        $this->info('já foi');
                    }
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                $bar->advance();
            }
        });

        $bar->finish();
    }
}
