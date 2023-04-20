<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("company_bank_accounts", function (Blueprint $table) {
            $table
                ->string("bank_ispb", 10)
                ->default(null)
                ->after("bank");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("company_bank_accounts", function (Blueprint $table) {
            $table->dropColumn("bank_ispb");
        });
    }
};
