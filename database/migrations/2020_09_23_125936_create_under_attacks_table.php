<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnderAttacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("under_attacks", function (Blueprint $table) {
            $table->id();
            $table->integer("domain_id")->unsigned();
            $table->timestamp("removed_at")->nullable();
            $table->timestamps();
        });

        Schema::table("under_attacks", function (Blueprint $table) {
            $table
                ->foreign("domain_id")
                ->references("id")
                ->on("domains");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("under_attacks");
    }
}
