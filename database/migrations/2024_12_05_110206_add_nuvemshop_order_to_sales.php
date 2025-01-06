<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table
                ->string("nuvemshop_order")
                ->after("woocommerce_order")
                ->nullable()
                ->index();
        });
    }

    public function down()
    {
        Schema::table("sales", function (Blueprint $table) {
            $table->dropColumn("nuvemshop_order");
        });
    }
};
