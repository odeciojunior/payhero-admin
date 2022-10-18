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
        Schema::create("iugu_credit_card_charges", function (Blueprint $table) {
            $table->id();
            $table
                ->unsignedBigInteger("sale_id")
                ->index()
                ->nullable();
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table->string("token_id",100);
            $table->string("brand",50)->nullable();
            $table->string("customer_id",100);
            $table->string("payment_id",100);
            $table->string("invoice_id",100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("iugu_credit_card_charges");
    }
};
