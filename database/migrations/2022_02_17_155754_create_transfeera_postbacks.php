<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfeeraPostbacks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("transfeera_postbacks", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("withdrawal_id")->nullable();
            $table
                ->foreign("withdrawal_id")
                ->references("id")
                ->on("withdrawals");
            $table->enum("source", ["payment", "contacerta"])->default("payment");
            $table->json("data");
            $table->tinyinteger("processed_flag")->default(0);
            $table->json("machine_result")->nullable();
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
        Schema::dropIfExists("transfeera_postbacks");
    }
}
