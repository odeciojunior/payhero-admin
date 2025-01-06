<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;

class DemoPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $plan = Plan::create([
                "project_id" => $product->project_id,
                "name" => $product->name,
                "description" => $product->description,
                "price" => $product->price,
                "status" => 1,
                "active_flag" => 1,
                "processing_cost" => 0,
            ]);

            $plan->update(["code" => $plan->id_code]);

            ProductPlan::create([
                "product_id" => $product->id,
                "plan_id" => $plan->id,
                "amount" => 1,
            ]);
        }
    }
}
