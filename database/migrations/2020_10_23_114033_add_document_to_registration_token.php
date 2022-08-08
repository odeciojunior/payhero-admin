<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentToRegistrationToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("registration_token", function (Blueprint $table) {
            $table
                ->string("document")
                ->nullable()
                ->after("token");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("registration_token", function (Blueprint $table) {
            $table->dropColumn("document");
        });
    }
}
