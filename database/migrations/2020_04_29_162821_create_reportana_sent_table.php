<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportanaSentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reportana_sent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('data');
            $table->text('response');
            $table->integer('sent_status')->nullable();
            $table->integer('event_sale')->unsigned()->nullable();
            $table->bigInteger('instance_id')->unsigned()->nullable();
            $table->string('instance')->nullable();
            $table->bigInteger('reportana_integration_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('reportana_sent', function(Blueprint $table) {
            $table->foreign('reportana_integration_id')->references('id')->on('reportana_integrations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reportana_sent');
    }
}
