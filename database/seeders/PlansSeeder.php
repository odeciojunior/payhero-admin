<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Plan;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\ProductPlan;
use Modules\Core\Entities\Project;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $project = Project::orderBy("id", "desc")->first();

        $plan = Plan::create([
            "project_id" => $project->id,
            "name" => "Primeiro plano",
            "description" => null,
            "price" => 5,
            "status" => 1,
            "active_flag" => 1,
            "processing_cost" => 0,
        ]);

        $plan->update(["code" => $plan->id_code]);

        $products = Product::all();
        foreach ($products as $product) {
            ProductPlan::create([
                "product_id" => $product["id"],
                "plan_id" => $plan->id,
                "amount" => 1,
            ]);
        }
    }
}
