<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivecampaignEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activecampaign_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('activecampaign_integration_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('plan_id')->unsigned()->nullable();
            $table->integer('event_sale');
            $table->string('add_tags')->nullable();
            $table->string('remove_tags')->nullable();
            $table->string('remove_list')->nullable();
            $table->string('add_list')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('activecampaign_events', function(Blueprint $table) {
            $table->foreign('activecampaign_integration_id')->references('id')->on('activecampaign_integrations');
        });

        Schema::table('activecampaign_events', function(Blueprint $table) {
            $table->foreign('plan_id')->references('id')->on('plans');
        });

        Schema::table('activecampaign_events', function(Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activecampaign_events');
    }
}
