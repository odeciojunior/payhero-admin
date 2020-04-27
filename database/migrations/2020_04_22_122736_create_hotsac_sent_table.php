<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotsacSentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotsac_sent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('data');
            $table->text('response');
            $table->integer('sent_status')->nullable();
            $table->integer('event_sale')->unsigned()->nullable();
            $table->bigInteger('instance_id')->unsigned()->nullable();
            $table->string('instance')->nullable();
            $table->bigInteger('hotsac_integration_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('hotsac_sent', function(Blueprint $table) {
            $table->foreign('hotsac_integration_id')->references('id')->on('hotsac_integrations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotsac_sent');
    }
}
