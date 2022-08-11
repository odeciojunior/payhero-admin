<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPixExpiredTableWhatsapp2Integrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("whatsapp2_integrations", function (Blueprint $table) {
            $table
                ->integer("pix_expired")
                ->after("abandoned_cart")
                ->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("whatsapp2_integrations", function (Blueprint $table) {
            $table->dropColumn(["pix_expired"]);
        });
    }
}
