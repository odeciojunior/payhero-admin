<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeConvertaxIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('convertax_integrations', function(Blueprint $table) {
            $table->boolean('abandoned_cart')->after('credit_card_paid')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('convertax_integrations', function(Blueprint $table) {
            $table->dropColumn('abandoned_cart');
        });
    }
}
