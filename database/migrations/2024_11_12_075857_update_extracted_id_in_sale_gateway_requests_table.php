<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            // Check if the column does not exist before adding it
            if (!Schema::hasColumn('sale_gateway_requests', 'extracted_id')) {
                // Add the extracted_id column as a generated column
                $table->string('extracted_id', 255)
                      ->nullable(); // Allow null values to avoid issues with default values
            }

            // Check if the index does not exist before creating it
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('sale_gateway_requests');
            if (!array_key_exists('idx_gateway_extracted', $indexes)) {
                // Create the index on gateway_id and extracted_id
                $table->index(['gateway_id', 'extracted_id'], 'idx_gateway_extracted');
            }
        });

        // Update the extracted_id column with the value extracted from gateway_result
        DB::statement("
            UPDATE sale_gateway_requests
            SET extracted_id = JSON_UNQUOTE(JSON_EXTRACT(gateway_result, '$.id'))
            WHERE JSON_UNQUOTE(JSON_EXTRACT(gateway_result, '$.id')) IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_gateway_requests', function (Blueprint $table) {
            // Drop the index if it exists
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('sale_gateway_requests');
            if (array_key_exists('idx_gateway_extracted', $indexes)) {
                $table->dropIndex('idx_gateway_extracted');
            }

            // Drop the column if it exists
            if (Schema::hasColumn('sale_gateway_requests', 'extracted_id')) {
                $table->dropColumn('extracted_id');
            }
        });
    }
}