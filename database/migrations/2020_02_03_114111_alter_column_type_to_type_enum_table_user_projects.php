<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnTypeToTypeEnumTableUserProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_projects', function (Blueprint $table) {
            $table->integer('type_enum')->default(0)->after('company_id');
            $table->integer('status_flag')->default(0)->after('edit_permission');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_projects', function (Blueprint $table) {
            $table->dropColumn('type_enum');
            $table->dropColumn('status_flag');
        });
    }
}
