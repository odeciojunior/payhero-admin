<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Project;

class UpdateProjectsNotazzConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $projects = Project::select("id", "cost_currency_type")->chunk(500, function ($projects) {
            foreach ($projects as $project) {
                $config = json_encode([
                    "cost_currency_type" => $project->cost_currency_type,
                    "update_cost_shopify" => 1,
                ]);
                $project->update(["notazz_configs" => $config]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
