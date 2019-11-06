<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGateways extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('gateways', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedTinyInteger('gateway_enum')->index();
            $table->string('name');
            $table->json('json_config');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateways');
    }
}
