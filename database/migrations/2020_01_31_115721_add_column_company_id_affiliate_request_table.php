<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCompanyIdAffiliateRequestTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('affiliate_requests', function(Blueprint $table) {
            $table->unsignedInteger('company_id')->nullable()->after('project_id');
        });
        Schema::table('affiliate_requests', function(Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('affiliate_requests', function(Blueprint $table) {
            $table->dropForeign(["company_id"]);
            $table->dropColumn('company_id');
        });
    }
}
