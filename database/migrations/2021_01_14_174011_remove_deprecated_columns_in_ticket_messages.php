<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveDeprecatedColumnsInTicketMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("ticket_messages", function (Blueprint $table) {
            $table->dropColumn(["from_admin", "from_system"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("ticket_messages", function (Blueprint $table) {
            $table->boolean("from_admin");
            $table->boolean("from_system");
        });
    }
}
