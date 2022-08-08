<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableManagerToSiriusLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("manager_to_sirius_logins", function (Blueprint $table) {
            $table->id();

            $table->integer("manager_user_id")->unsigned();
            $table
                ->foreign("manager_user_id")
                ->references("id")
                ->on("users");

            $table->integer("sirius_user_id")->unsigned();
            $table
                ->foreign("sirius_user_id")
                ->references("id")
                ->on("users");

            $table->boolean("is_active")->default(true);
            $table
                ->string("token", 60)
                ->nullable()
                ->unique()
                ->default(null);

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
        Schema::dropIfExists("manager_to_sirius_logins");
    }
}
