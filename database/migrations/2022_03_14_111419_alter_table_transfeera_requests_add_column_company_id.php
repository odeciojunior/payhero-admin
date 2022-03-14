<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableTransfeeraRequestsAddColumnCompanyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfeera_requests',function(Blueprint $table){
            $table->unsignedInteger('company_id')->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->enum('source',['payment','contacerta'])->default('payment')->after('response');
        });

        Schema::table('transfeera_postbacks',function(Blueprint $table){
            $table->unsignedInteger('company_id')->nullable()->default(null)->after('id');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfeera_requests',function(Blueprint $table){
            $table->dropForeign('company_id');
            $table->dropColumn('company_id');            
        });

        Schema::table('transfeera_postbacks',function(Blueprint $table){
            $table->dropForeign('company_id');
            $table->dropColumn('company_id');            
        });
    }
}
