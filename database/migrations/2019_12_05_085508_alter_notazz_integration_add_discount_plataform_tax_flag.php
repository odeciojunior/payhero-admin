<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNotazzIntegrationAddDiscountPlataformTaxFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->unsignedTinyInteger('discount_plataform_tax_flag')->after('pending_days')->default(0); // 0 - false, 1 - true

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notazz_integrations', function(Blueprint $table) {
            $table->dropColumn('discount_plataform_tax_flag');
        });
    }
}
