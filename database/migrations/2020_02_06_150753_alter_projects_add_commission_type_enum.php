<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectsAddCommissionTypeEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->tinyInteger('commission_type_enum')->after('terms_affiliates')->default(2);
            $table->tinyInteger('status_url_affiliates')->after('terms_affiliates')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('commission_type_enum');
            $table->dropColumn('status_url_affiliates');
        });
    }
}
