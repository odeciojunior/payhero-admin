<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivecampaignSentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activecampaign_sent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('data');
            $table->text('response');
            $table->integer('sent_status')->nullable();
            $table->integer('event_sale')->unsigned()->nullable();
            $table->bigInteger('sale_id')->unsigned()->nullable();
            $table->bigInteger('activecampaign_integration_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('activecampaign_sent', function(Blueprint $table) {
            $table->foreign('activecampaign_integration_id')->references('id')->on('activecampaign_integrations');
        });

        Schema::table('activecampaign_sent', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activecampaign_sent');
    }
}

// 