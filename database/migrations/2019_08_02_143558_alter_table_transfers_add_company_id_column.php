<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTransfersAddCompanyIdColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('transfers', function(Blueprint $table) {
            $table->unsignedInteger('company_id')->nullable()->after('user');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function(Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
}
