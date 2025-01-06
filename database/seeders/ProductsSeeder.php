<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereNull("is_cloudfox")->first();
        $project = UserProject::select("project_id")
            ->where("user_id", $user->id)
            ->where("company_id", $user->company_default)
            ->where("status", "active")
            ->first();

        Product::create([
            "user_id" => $user->id,
            "name" => "Primeiro produto",
            "project_id" => $project->project_id,
        ]);
    }
}
