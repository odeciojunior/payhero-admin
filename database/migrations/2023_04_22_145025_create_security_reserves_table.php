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
        Schema::create("security_reserves", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("company_id");
            $table->unsignedBigInteger("sale_id");
            $table->unsignedBigInteger("transaction_id");
            $table->unsignedBigInteger("transfer_id");
            $table->unsignedInteger("user_id");
            $table->integer("value");
            $table->tinyInteger("status");
            $table->timestamp("release_date");
            $table->timestamp("released_at")->nullable();
            $table->integer("tax");
            $table->timestamps();

            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table
                ->foreign("transaction_id")
                ->references("id")
                ->on("transactions");
            $table
                ->foreign("transfer_id")
                ->references("id")
                ->on("transfers");
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("security_reserves");
    }
};
