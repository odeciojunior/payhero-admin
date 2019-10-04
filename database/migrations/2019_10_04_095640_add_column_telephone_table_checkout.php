<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTelephoneTableCheckout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->string('client_telephone')->nullable()->after('is_mobile');
        });
    }

    public function down()
    {
        Schema::table('checkouts', function(Blueprint $table) {
            $table->dropColumn('client_telephone');
        });
    }

}
