<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterGatewayBilletIdentificatorOnRegeneratedBillets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('regenerated_billets', function (Blueprint $table) {
            $table->string('gateway_billet_identificator')->nullable()->change();
            $table->index('gateway_billet_identificator');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('regenerated_billets', function (Blueprint $table) {
            $table->bigInteger('gateway_billet_identificator')->nullable()->change();
            $table->dropIndex(['gateway_billet_identificator']);
        });
    }
}
