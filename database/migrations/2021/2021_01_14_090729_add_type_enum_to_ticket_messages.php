<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeEnumToTicketMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("ticket_messages", function (Blueprint $table) {
            $table
                ->integer("type_enum")
                ->default(1)
                ->after("message");
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
            $table->dropColumn("type_enum");
        });
    }
}
