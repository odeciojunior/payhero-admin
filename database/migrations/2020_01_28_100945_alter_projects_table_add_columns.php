<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProjectsTableAddColumns extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function(Blueprint $table) {
            $table->boolean("discount_recovery_status")->after("cost_currency_type")->default(0)
                  ->comment('True (Está ativa a recobrança) - False (Não está ativa a recobrança)');
            $table->integer("discount_recovery_value")->after("discount_recovery_status")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function(Blueprint $table) {
            $table->dropColumn(["discount_recovery_status"]);
            $table->dropColumn(["discount_recovery_value"]);
        });
    }
}
