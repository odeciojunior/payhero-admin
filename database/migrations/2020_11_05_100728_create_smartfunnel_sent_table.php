<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmartfunnelSentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("smartfunnel_sent", function (Blueprint $table) {
            $table->id();
            $table->text("data");
            $table->text("response");
            $table->integer("sent_status")->nullable();
            $table
                ->integer("event_sale")
                ->unsigned()
                ->nullable();
            $table
                ->bigInteger("sale_id")
                ->unsigned()
                ->nullable();
            $table
                ->bigInteger("smartfunnel_integration_id")
                ->unsigned()
                ->nullable();
            $table->timestamps();
        });

        Schema::table("smartfunnel_sent", function (Blueprint $table) {
            $table
                ->foreign("smartfunnel_integration_id")
                ->references("id")
                ->on("smartfunnel_integrations");
        });

        Schema::table("smartfunnel_sent", function (Blueprint $table) {
            $table
                ->foreign("sale_id")
                ->references("id")
                ->on("sales");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("smartfunnel_sent");
    }
}
