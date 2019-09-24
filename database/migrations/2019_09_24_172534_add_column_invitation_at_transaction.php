<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInvitationAtTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function($table) {
            $table->bigInteger('invitation_id')->unsigned()->nullable();

            $table->foreign('invitation_id')->references('id')->on('invitations');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {

        Schema::table('transactions', function($table) {
            $table->dropForeign(['invitation_id']);

            $table->dropColumn('invitation_id');
        });
    }
}
