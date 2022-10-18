<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCodeMetaTagFacebookToPixelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("pixels", function (Blueprint $table) {
            $table
                ->string("code_meta_tag_facebook")
                ->nullable()
                ->after("apply_on_plans");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("pixels", function (Blueprint $table) {
            $table->dropColumn(["code_meta_tag_facebook"]);
        });
    }
}
