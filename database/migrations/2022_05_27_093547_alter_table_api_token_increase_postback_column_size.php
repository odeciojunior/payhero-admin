<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableApiTokenIncreasePostbackColumnSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("api_tokens", function (Blueprint $table) {
            $table->string("postback", 1000)->change();
        });

        Schema::table("checkout_configs", function (Blueprint $table) {
            $table->index("project_id");
            $table->index("company_id");
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
