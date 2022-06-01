<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAndCompanyBureauResultColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('bureau_result')->after('id_wall_result')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->json('bureau_result')->after('id_wall_date_update')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bureau_result');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('bureau_result');
        });
    }
}
