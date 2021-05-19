<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDomainRecordsAddMxPriorityColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains_records', function(Blueprint $table) {
            $table->integer('priority')->after('system_flag')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains_records', function(Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
}
