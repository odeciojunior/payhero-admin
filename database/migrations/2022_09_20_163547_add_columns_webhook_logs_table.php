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
        Schema::table("webhook_logs", function (Blueprint $table) {
            $table
                ->unsignedBigInteger("sale_id")
                ->index()
                ->nullable()
                ->after("company_id");
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table
                ->integer("response_status")
                ->nullable()
                ->after("response");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("webhook_logs", function (Blueprint $table) {
            $table->dropForeign(["sale_id"]);
            $table->dropColumn("sale_id");
            $table->dropColumn("response_status");
        });
    }
};
