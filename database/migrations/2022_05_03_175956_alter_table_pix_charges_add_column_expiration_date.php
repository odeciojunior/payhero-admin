<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePixChargesAddColumnExpirationDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("pix_charges", function (Blueprint $table) {
            $table
                ->timestamp("expiration_date")
                ->nullable()
                ->after("status");
            $table
                ->integer("automatic_discount")
                ->nullable()
                ->default(null)
                ->after("expiration_date");
            $table
                ->integer("total_pix_value")
                ->nullable()
                ->default(null)
                ->after("qrcode_image");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
