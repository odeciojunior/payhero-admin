<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPixKeySituationToCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table
                ->enum("pix_key_situation", ["NO_KEY", "WAITING_VERIFICATION", "VERIFIED", "WRONG_KEY"])
                ->default("NO_KEY")
                ->after("has_pix_key");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->dropColumn("pix_key_situation");
        });
    }
}
