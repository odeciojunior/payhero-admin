<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnicodropIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create("unicodrop_integrations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->unsignedInteger("project_id")->index();
            $table->string("token");
            $table->boolean("abandoned_cart")->default(true);
            $table->boolean("billet_paid")->default(true);
            $table->boolean("billet_generated")->default(true);
            $table->boolean("credit_card_paid")->default(true);
            $table->boolean("credit_card_refused")->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table("unicodrop_integrations", function (Blueprint $table) {
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects");
        });

        Schema::table("unicodrop_integrations", function (Blueprint $table) {
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("unicodrop_integrations");
    }
}
