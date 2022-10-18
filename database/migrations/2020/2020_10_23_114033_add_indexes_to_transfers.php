<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("transfers", function (Blueprint $table) {
            $table->index("value");
            $table->index("is_refund_tax");
            $table->index("created_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("transfers", function (Blueprint $table) {
            $table->dropIndex(["value"]);
            $table->dropIndex(["created_at"]);
            $table->dropIndex(["is_refund_tax"]);
        });
    }
}
