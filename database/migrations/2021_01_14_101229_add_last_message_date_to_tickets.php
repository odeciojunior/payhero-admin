<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastMessageDateToTickets extends Migration
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
                ->timestamp("last_message_date")
                ->useCurrent()
                ->after("last_message_type_enum");
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
            $table->dropColumn("last_message_date");
        });
    }
}
