<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserved_sales', function (Blueprint $table) {
            $table->enum("type",["CHARGEBACK","REFUNDED"])->default("CHARGEBACK")->after("reason");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reserved_sale', function (Blueprint $table) {
            $table->dropColumn("type");
        });
    }
};
