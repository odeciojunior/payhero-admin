<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUpsellConfigs extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('project_upsell_configs', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id')->index();
            $table->string('header');
            $table->string('title');
            $table->text('description');
            $table->integer('countdown_time')->nullable();
            $table->boolean('countdown_flag')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('project_upsell_configs', function(Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('upsell_configs');
    }
}
