<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\DiscountCoupon;
use Modules\Core\Entities\Project;

class DicountCouponsRecoverySeeder extends Seeder
{
    public function run()
    {
        $projects = Project::where("status", Project::STATUS_ACTIVE)->get();
        foreach ($projects as $project) {
            DiscountCoupon::create([
                "project_id"            => $project->id,
                "name"                  => "Desconto 10%",
                "type"                  => 0,
                "value"                 => 10,
                "code"                  => "NEXX10",
                "status"                => 1,
                "rule_value"            => 0,
                "recovery_flag"         => true,
            ]);

            DiscountCoupon::create([
                "project_id"            => $project->id,
                "name"                  => "Desconto 20%",
                "type"                  => 0,
                "value"                 => 20,
                "code"                  => "NEXX20",
                "status"                => 1,
                "rule_value"            => 0,
                "recovery_flag"         => true,
            ]);
        }
    }
}
