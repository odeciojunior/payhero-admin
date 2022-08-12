<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePixKeyColumnToCompaniesTable extends Migration
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
                ->boolean("pix_key")
                ->default(false)
                ->change();
        });
        Schema::table("companies", function (Blueprint $table) {
            $table->renameColumn("pix_key", "has_pix_key");
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
            $table->renameColumn("has_pix_key", "pix_key");
        });

        Schema::table("companies", function (Blueprint $table) {
            $table
                ->string("pix_key")
                ->after("gateway_tax")
                ->nullable()
                ->change();
        });
    }
}
