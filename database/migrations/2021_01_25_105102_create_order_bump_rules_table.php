<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBumpRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("order_bump_rules", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("project_id");
            $table->text("description")->nullable();
            $table->integer("discount");
            $table->json("apply_on_plans")->nullable();
            $table->json("offer_plans");
            $table->boolean("active_flag")->default(0);
            $table->timestamps();
        });

        Schema::table("order_bump_rules", function (Blueprint $table) {
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("order_bump_rules");
    }
}
