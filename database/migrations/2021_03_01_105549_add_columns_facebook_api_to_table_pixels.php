<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsFacebookApiToTablePixels extends Migration
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
                ->boolean("is_api")
                ->default(false)
                ->after("purchase_event_name");
            $table
                ->text("facebook_token")
                ->nullable()
                ->after("is_api");
            $table
                ->integer("value_percentage_purchase_boleto")
                ->default(100)
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
            $table->dropColumn(["is_api", "facebook_token", "value_percentage_purchase_boleto"]);
        });
    }
}
