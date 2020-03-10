<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectUpsellRules extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('project_upsell_rules', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id')->index();
            $table->text('description')->nullable();
            $table->json('apply_on_plans')->nullable();
            $table->json('offer_on_plans')->nullable();
            $table->boolean('active_flag')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('project_upsell_rules', function(Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_upsell_rules');
    }
}
