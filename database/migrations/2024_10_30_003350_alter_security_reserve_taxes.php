<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSecurityReserveTaxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('security_reserve_tax')->default(30)->change();
            $table->integer('security_reserve_tax_pix')->default(10)->change();
            $table->integer('security_reserve_tax_billet')->default(10)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('security_reserve_tax')->default(20)->change();
            $table->integer('security_reserve_tax_pix')->default(20)->change();
            $table->integer('security_reserve_tax_billet')->default(20)->change();
        });
    }
}
