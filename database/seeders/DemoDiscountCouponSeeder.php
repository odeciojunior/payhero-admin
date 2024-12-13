<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Plan;

class DemoDiscountCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plan = Plan::where("project_id", 1)
            ->inRandomOrder()
            ->first();

        $planId = hashids_encode($plan->id);
        DiscountCoupon::create([
            "project_id" => 1,
            "name" => "Cupom Demo",
            "type" => 0,
            "value" => 10,
            "code" => "DEMO10",
            "status" => 1,
            "plans" =>
                '[{"id": "' .
                $planId .
                '", "name": "' .
                $plan->name .
                '", "image": "/build/global/img/produto.svg", "description": ""}]',
            "discount" => 0,
            "rule_value" => 500,
        ]);
    }
}
