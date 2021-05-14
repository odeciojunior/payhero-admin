<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhatsapp2SentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp2_sent', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('data');
            $table->text('response');
            $table->integer('sent_status')->nullable();
            $table->integer('event_sale')->unsigned()->nullable();
            $table->bigInteger('instance_id')->unsigned()->nullable();
            $table->string('instance')->nullable();
            $table->bigInteger('whatsapp2_integration_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('whatsapp2_sent', function(Blueprint $table) {
            $table->foreign('whatsapp2_integration_id')->references('id')->on('whatsapp2_integrations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp2_sent');
    }
}
