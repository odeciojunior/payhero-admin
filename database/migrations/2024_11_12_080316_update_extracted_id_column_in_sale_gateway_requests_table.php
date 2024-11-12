<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateExtractedIdColumnInSaleGatewayRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the extracted_id column with the value extracted from gateway_result
        DB::statement("
            UPDATE sale_gateway_requests
            SET extracted_id = IFNULL(JSON_UNQUOTE(JSON_EXTRACT(gateway_result, '$.id')),'0')
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally, you can revert the changes made in the up method
        // For example, setting extracted_id to NULL
        DB::statement("
            UPDATE sale_gateway_requests
            SET extracted_id = NULL
        ");
    }
}