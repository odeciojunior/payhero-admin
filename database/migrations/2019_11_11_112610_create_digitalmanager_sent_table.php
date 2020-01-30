<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDigitalmanagerSentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digitalmanager_sent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('data');
            $table->text('response');
            $table->integer('sent_status')->nullable();
            $table->integer('event_sale')->unsigned()->nullable();
            $table->bigInteger('instance_id')->unsigned()->nullable();
            $table->string('instance')->nullable();
            $table->bigInteger('digitalmanager_integration_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('digitalmanager_sent', function(Blueprint $table) {
            $table->foreign('digitalmanager_integration_id')->references('id')->on('digitalmanager_integrations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('digitalmanager_sent');
    }
}
