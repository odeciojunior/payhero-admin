<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCompanyTypeTableCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function(Blueprint $table) {
            $table->unsignedInteger('company_type')->nullable()->after('contract_document_status'); //1- physical person, 2- juridical person
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function(Blueprint $table) {
            $table->dropColumn('company_type');
        });
    }
}
