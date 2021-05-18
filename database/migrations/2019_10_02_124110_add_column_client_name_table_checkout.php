<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class AddColumnClientNameTableCheckout
 */
class AddColumnClientNameTableCheckout extends Migration
{
    public function up()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->string('client_name')->nullable()->after('is_mobile');
        });
    }

    public function down()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->dropColumn('client_name');
        });
    }
}
