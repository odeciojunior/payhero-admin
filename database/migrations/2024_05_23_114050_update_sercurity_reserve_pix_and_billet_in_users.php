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
        DB::statement("UPDATE `users` SET 
        security_reserve_tax_pix=security_reserve_tax
        ,security_reserve_days_pix=security_reserve_days
        ,security_reserve_tax_billet=security_reserve_tax
        ,security_reserve_days_billet=security_reserve_days
        ,updated_at =NOW();");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE `users` SET 
        security_reserve_tax_pix=null
        ,security_reserve_days_pix=null
        ,security_reserve_tax_billet=null
        ,security_reserve_days_billet=null
        ,updated_at =NOW();");
    }
};
