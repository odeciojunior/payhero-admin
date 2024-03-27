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
        Schema::create("gateway_users", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->unsignedBigInteger("billet_gateway_id")->nullable();
            $table->unsignedBigInteger("credit_card_gateway_id")->nullable();
            $table->unsignedBigInteger("pix_gateway_id")->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

            $table
                ->foreign(["billet_gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

            $table
                ->foreign(["credit_card_gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

            $table
                ->foreign(["pix_gateway_id"])
                ->references(["id"])
                ->on("gateways")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("gateway_users");
    }
};
