<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableDomainAddCloudFlareIdColumn extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function(Blueprint $table) {
            $table->string('cloudflare_domain_id')->after('project_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function(Blueprint $table) {
            $table->dropColumn('cloudflare_domain_id');
        });
    }
}
