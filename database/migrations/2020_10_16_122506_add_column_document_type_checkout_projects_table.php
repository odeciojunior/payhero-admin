<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDocumentTypeCheckoutProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("projects", function (Blueprint $table) {
            $table->unsignedInteger("document_type_checkout")->default(3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("projects", function (Blueprint $table) {
            $table->dropColumn("document_type_checkout");
        });
    }
}
