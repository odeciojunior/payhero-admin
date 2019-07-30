<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotzappIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotzapp_integrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('link');

            $table->boolean('boleto_generated')->default(true);
            $table->boolean('boleto_paid')->default(true);
            $table->boolean('credit_card_refused')->default(true);
            $table->boolean('credit_card_paid')->default(true);

            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotzapp_integrations');
    }
}
