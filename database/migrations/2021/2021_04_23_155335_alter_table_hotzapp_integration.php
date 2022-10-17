<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableHotzappIntegration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE hotzapp_integrations ADD COLUMN pix_generated tinyint default 1 after abandoned_cart,
        ADD COLUMN pix_paid TINYINT DEFAULT 1 AFTER pix_generated;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE hotzapp_integrations DROP COLUMN pix_generated,
        DROP COLUMN pix_paid;");
    }
}
