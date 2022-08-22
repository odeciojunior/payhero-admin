<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropValuePerncetagePurchasePixTablePixels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("pixels", function (Blueprint $table) {
            $table->dropColumn(["value_percentage_purchase_pix"]);
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
            $table
                ->integer("value_percentage_purchase_pix")
                ->default(100)
                ->after("value_percentage_purchase_boleto");
        });
    }
}
