<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCompaniesAddStatusColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function(Blueprint $table) {
            $table->tinyInteger('contract_document_status')->after('balance')->default(1);
            $table->tinyInteger('address_document_status')->after('balance')->default(1);
            $table->tinyInteger('bank_document_status')->after('balance')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function(Blueprint $table) {
            $table->dropColumn('contract_document_status');
            $table->dropColumn('address_document_status');
            $table->dropColumn('bank_document_status');
        });
    }
}
