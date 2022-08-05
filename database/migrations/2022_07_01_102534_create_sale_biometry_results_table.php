<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleBiometryResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_biometry_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales');
            $table->string('vendor')->index();
            $table->string('biometry_id')->nullable()->index();
            $table->string('score')->nullable();
            $table->string('status')->nullable()->index();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->json('postback_data')->nullable();
            $table->json('api_data')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_biometry_results');
    }
}
