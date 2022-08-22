<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBenefitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("user_benefits", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id");
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table->unsignedBigInteger("benefit_id");
            $table
                ->foreign("benefit_id")
                ->references("id")
                ->on("benefits");
            $table->boolean("enabled")->default(false);
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
        Schema::dropIfExists("user_benefits");
    }
}
