<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCompanyTableFanstayNameNotNullableColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function(Blueprint $table) {
            $table->string('fantasy_name', 255)->nullable(false)->change();
            $table->string('country', 255)->default('brazil')->change();
            $table->renameColumn('cnpj', 'company_document');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function(Blueprint $table) {
            $table->string('country', 255)->default('brasil')->change();
            $table->string('fantasy_name', 255)->nullable()->change();
            $table->renameColumn('company_document', 'cnpj');
        });
    }
}

