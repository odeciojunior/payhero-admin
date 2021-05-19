<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserTerms extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('user_terms', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->string('term_version')->nullable();
            $table->json('device_data')->nullable();
            $table->boolean('accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('user_terms', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('table_user_terms', function(Blueprint $table) {
            $table->dropForeign(["user_id"]);
        });

        Schema::dropIfExists('table_user_terms');
    }
}
