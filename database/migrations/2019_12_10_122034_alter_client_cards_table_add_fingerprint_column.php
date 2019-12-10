<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClientCardsTableAddFingerprintColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table("client_cards", function(Blueprint $table) {
            $table->string("browser_fingerprint")->nullable()->after("id");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table("client_cards", function(Blueprint $table) {
            $table->dropColumn(["browser_fingerprint"]);
        });
    }
}
