<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivecampaignCustomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activecampaign_customs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('activecampaign_integration_id')->unsigned();
            $table->bigInteger('custom_field_id')->unsigned();
            $table->string('custom_field');
            $table->timestamps();
        });

        Schema::table('activecampaign_customs', function(Blueprint $table) {
            $table->foreign('activecampaign_integration_id')->references('id')->on('activecampaign_integrations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activecampaign_customs');
    }
}
