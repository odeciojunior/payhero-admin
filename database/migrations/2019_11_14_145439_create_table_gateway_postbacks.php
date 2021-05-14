<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGatewayPostbacks extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_postbacks', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id')->index()->nullable();
            $table->unsignedBigInteger('gateway_id')->index()->nullable();
            $table->string('reference_id')->nullable();
            $table->json('data');
            $table->unsignedTinyInteger('gateway_enum')->index();
            $table->string('gateway_postback_type')->nullable();
            $table->string('gateway_status')->nullable();
            $table->string('gateway_payment_type')->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->unsignedTinyInteger('processed_flag')->index()->default(0); // 0-no , 1-yes
            $table->unsignedTinyInteger('postback_valid_flag')->index()->default(0); // 0-no , 1-yes

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->foreign('sale_id')->references('id')->on('sales');
        });

        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->foreign('gateway_id')->references('id')->on('gateways');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('gateway_postbacks', function(Blueprint $table) {
            $table->dropForeign(['sale_id']);
        });

        Schema::dropIfExists('gateway_postbacks');
    }
}
