<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionCloudfoxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("transaction_cloudfox", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table
                ->bigInteger("sale_id")
                ->unsigned()
                ->nullable();
            $table
                ->bigInteger("gateway_id")
                ->unsigned()
                ->nullable();
            $table
                ->integer("company_id")
                ->unsigned()
                ->nullable();
            $table
                ->integer("user_id")
                ->unsigned()
                ->nullable();

            $table->string("value");
            $table->string("value_total");
            $table->string("status")->default("paid");
            $table->integer("status_enum")->default(2);

            $table->date("release_date");
            $table->dateTime("gateway_released_at")->nullable();
            $table->dateTime("gateway_transferred_at")->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
            $table
                ->foreign("gateway_id")
                ->references("id")
                ->on("gateways");
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
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
        Schema::dropIfExists("transaction_cloudfox");
    }
}
