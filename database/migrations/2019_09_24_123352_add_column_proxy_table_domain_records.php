<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProxyTableDomainRecords extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('domains_records', function($table) {
            $table->integer('proxy')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('domains_records', function($table) {
            $table->dropForeign(['proxy']);
        });
    }
}
