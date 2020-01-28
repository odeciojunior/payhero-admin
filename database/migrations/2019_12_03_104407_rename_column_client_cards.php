<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnClientCards extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('client_cards', function(Blueprint $table) {
            $table->renameColumn('first_four_digits', 'first_six_digits');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('client_cards', function(Blueprint $table) {
            $table->renameColumn('first_six_digits', 'first_four_digits');
        });
    }
}
