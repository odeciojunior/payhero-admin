<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivecampaignIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activecampaign_integrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('project_id')->index();
            $table->string('api_url');
            $table->string('api_key');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('activecampaign_integrations', function(Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('activecampaign_integrations', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activecampaign_integrations');
    }
}
