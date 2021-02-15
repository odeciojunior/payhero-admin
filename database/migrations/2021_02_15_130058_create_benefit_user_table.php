<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBenefitUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benefit_user', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('benefit_id');
            $table->primary(['benefit_id','user_id']);
            $table->timestamps();
        });

        Schema::table('benefit_user', function (Blueprint $table) {
            $table->foreign('benefit_id')->references('id')->on('benefits');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('benefit_user');
    }
}
