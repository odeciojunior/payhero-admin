<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNsuToSaleContestationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("sale_contestations", function (Blueprint $table) {
            $table
                ->string("nsu")
                ->nullable()
                ->after("sale_id");
            $table
                ->date("file_date")
                ->nullable()
                ->after("nsu");
            $table
                ->date("transaction_date")
                ->nullable()
                ->after("file_date");
            $table
                ->date("request_date")
                ->nullable()
                ->after("transaction_date");
            $table
                ->string("reason")
                ->nullable()
                ->after("request_date");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sale_contestations", function (Blueprint $table) {
            $table->dropColumn(["nsu", "file_date", "transaction_date", "request_date", "reason"]);
        });
    }
}
