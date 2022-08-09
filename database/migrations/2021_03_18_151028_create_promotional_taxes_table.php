<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionalTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("promotional_taxes", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id");
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
            $table->date("expiration");
            $table->string("tax");
            $table->string("old_tax")->nullable();
            $table->boolean("active")->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("promotional_taxes");
    }
}
