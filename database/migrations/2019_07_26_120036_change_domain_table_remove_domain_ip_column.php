<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDomainTableRemoveDomainIpColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function(Blueprint $table) {
            $table->dropColumn('domain_ip');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function(Blueprint $table) {
            $table->string('domain_ip')->nullable();
        });
    }
}
