<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUrlFacebookDomainTablePixel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("pixels", function (Blueprint $table) {
            $table
                ->string("url_facebook_domain")
                ->nullable()
                ->after("facebook_token");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("pixels", function (Blueprint $table) {
            $table->dropColumn(["url_facebook_domain"]);
        });
    }
}
