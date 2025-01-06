<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToGatewayIdOnSaleGatewayRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_gateway_requests', function (Blueprint $table) {
            $table->index('gateway_id'); // Adiciona o índice na coluna gateway_id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_gateway_requests', function (Blueprint $table) {
            $table->dropIndex(['gateway_id']); // Remove o índice na coluna gateway_id
        });
    }
}