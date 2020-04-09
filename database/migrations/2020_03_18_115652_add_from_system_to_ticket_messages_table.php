<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFromSystemToTicketMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->boolean('from_system')
                ->default(false)
                ->after('from_admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket_messages', function (Blueprint $table) {
            $table->dropColumn('from_system');
        });
    }
}
