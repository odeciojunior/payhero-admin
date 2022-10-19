<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastMessageTypeEnumToTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("tickets", function (Blueprint $table) {
            $table
                ->integer("last_message_type_enum")
                ->default(1)
                ->after("ticket_status_enum");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("tickets", function (Blueprint $table) {
            $table->dropColumn("last_message_type_enum");
        });
    }
}
