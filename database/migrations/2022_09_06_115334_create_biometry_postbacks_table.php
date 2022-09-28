<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biometry_postbacks', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->unsignedInteger("user_id")
                ->index()
                ->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table
                ->unsignedBigInteger("user_biometry_resut_id")
                ->index()
                ->nullable();
            $table->foreign('user_biometry_resut_id')->references('id')->on('user_biometry_results');
            $table->string('vendor')->index();
            $table->json('postback_data')->nullable();
            $table->json('api_data')->nullable();
            $table
                ->unsignedTinyInteger('processed_flag')
                ->index()
                ->default(0); // 0-no , 1-yes
            $table->dateTime("date_api_data")
                ->nullable()
                ->default(null);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biometry_postbacks');
    }
};
