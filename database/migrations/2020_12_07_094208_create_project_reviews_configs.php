<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectReviewsConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_reviews_configs', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id')->index();
            $table->string('icon_type', 20)->default('star');
            $table->string('icon_color',7)->default('#F8CE1C');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('project_reviews_configs', function(Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_reviews_configs');
    }
}
