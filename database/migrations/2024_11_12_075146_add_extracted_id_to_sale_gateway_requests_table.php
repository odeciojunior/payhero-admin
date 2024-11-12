<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtractedIdToSaleGatewayRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_gateway_requests', function (Blueprint $table) {
            // Adiciona a coluna generated
            $table->string('extracted_id', 255)
                  ->generatedAs("JSON_UNQUOTE(JSON_EXTRACT(gateway_result, '$.id'))")
                  ->stored();

            // Cria o índice
            $table->index(['gateway_id', 'extracted_id'], 'idx_gateway_extracted');
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
            // Remove o índice
            $table->dropIndex('idx_gateway_extracted');

            // Remove a coluna generated
            $table->dropColumn('extracted_id');
        });
    }
}