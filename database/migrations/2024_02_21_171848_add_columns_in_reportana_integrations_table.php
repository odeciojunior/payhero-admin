<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reportana_integrations', function (Blueprint $table) {
            $table->text('client_id')->require();
            $table->text('client_secret')->require();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reportana_integrations', function (Blueprint $table) {
            $table->$table->dropColumn('client_id');
            $table->$table->dropColumn('client_secret');
        });
    }
};
