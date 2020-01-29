<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUselessTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('anticipated_transactions');
        Schema::dropIfExists('anticipations');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('carriers');
        Schema::dropIfExists('clients_cookie');
        Schema::dropIfExists('extra_materials');
        Schema::dropIfExists('gifts');
        Schema::dropIfExists('hubsmart_invitation_request');
        Schema::dropIfExists('layouts');
        Schema::dropIfExists('plan_gifts');
        Schema::dropIfExists('zenvia_sms');
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
