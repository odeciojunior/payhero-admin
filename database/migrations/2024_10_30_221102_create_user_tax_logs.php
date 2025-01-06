<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_tax_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("operator_id")->index();
            $table->json("properties");
            $table->timestamps();

            $table
                ->foreign(["user_id"])
                ->references(["id"])
                ->on("users")
                ->onUpdate("NO ACTION")
                ->onDelete("NO ACTION");

                $table
                ->foreign(["operator_id"])
                ->references(["id"])
                ->on("users")
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
        Schema::dropIfExists('user_tax_logs');
    }
};
