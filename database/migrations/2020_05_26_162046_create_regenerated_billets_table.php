<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegeneratedBilletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("regenerated_billets", function (Blueprint $table) {
            $table->id();
            $table->bigInteger("sale_id")->unsigned();
            $table->string("billet_link");
            $table->string("billet_digitable_line");
            $table->string("billet_due_date");
            $table->string("gateway_transaction_id");
            $table->bigInteger("gateway_billet_identificator")->nullable();
            $table->bigInteger("gateway_id")->unsigned();
            $table->bigInteger("owner_id");
            $table->timestamps();
        });

        Schema::table("regenerated_billets", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
        });

        Schema::table("regenerated_billets", function (Blueprint $table) {
            $table
                ->foreign("gateway_id")
                ->references("id")
                ->on("gateways");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("regenerated_billets");
    }
}
