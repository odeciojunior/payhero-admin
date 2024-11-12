<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateExtractedIdInSaleGatewayRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_gateway_requests', function (Blueprint $table) {
            // Add the extracted_id column as a generated column
            $table->string('extracted_id', 255)
                  ->generatedAs("JSON_UNQUOTE(JSON_EXTRACT(gateway_result, '$.id'))")
                  ->stored()
                  ->nullable(); // Allow null values to avoid issues with default values

            // Create the index on gateway_id and extracted_id
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
            // Drop the index
            $table->dropIndex('idx_gateway_extracted');

            // Drop the extracted_id column
            $table->dropColumn('extracted_id');
        });
    }
}