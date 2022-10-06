<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableWoocommerceIntegrationsSync extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create("woo_commerce_integrations_sync", function (Blueprint $table) {
            $table->bigIncrements("id");

            $table->longText("product_data");

            $table->integer("status")->default(0);

            $table->integer("total_products")->default(0);
            $table->integer("total_products_done")->default(0);
            $table->integer("total_products_sku_compared")->default(0);

            $table->integer("project_id")->unsigned();
            $table
                ->foreign("project_id")
                ->references("id")
                ->on("projects");

            $table->integer("user_id")->unsigned();
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");

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
        //
    }
}
