<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pixels', function (Blueprint $table) {
            $table->boolean('percentage_purchase_boleto_enabled')->default(false)->after('url_facebook_domain');
            $table->boolean('percentage_purchase_pix_enabled')->default(false)->after('value_percentage_purchase_boleto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pixels', function (Blueprint $table) {
            $table->dropColumn('percentage_purchase_boleto_enabled');
            $table->dropColumn('percentage_purchase_pix_enabled');
        });
    }
};
