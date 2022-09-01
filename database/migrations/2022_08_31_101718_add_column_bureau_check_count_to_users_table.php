<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->tinyInteger('bureau_check_count')
                    ->default(0)
                    ->after("bureau_result");
                $table->dateTime('bureau_data_updated_at')
                    ->nullable()
                    ->after('bureau_check_count');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                $table->dropColumn('bureau_check_count');
                $table->dropColumn('bureau_data_updated_at');
            }
        );
    }
};
