<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNotazzIntegrations extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('notazz_integrations', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->text('token_webhook');
            $table->text('token_api');
            $table->text('token_logistics');

            $table->timestamps();
        });

        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notazz_integrations');
    }
}
