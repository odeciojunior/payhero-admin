<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTemplateTypeTableCheckout extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->unsignedInteger('template_type')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->dropColumn('template_type');
        });
    }
}
