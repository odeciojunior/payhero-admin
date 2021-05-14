<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterWhiteBlackListTableAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('white_black_list', function(Blueprint $table) {
            $table->date('expires_at')->after('value')->nullable();
            $table->text('description')->after('expires_at')->nullable();
            $table->string("value")->comment("Valor a verificar na regra")->change();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('white_black_list', function(Blueprint $table) {
            $table->dropColumn(['expires_at', 'description']);
            $table->string("value")->comment("Valor a verificar na regra")->nullable()->change();
        });
    }
}
