<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserNotificationTableRenameColumnNewAffiliation extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('user_notifications', function(Blueprint $table) {
            $table->renameColumn('new_affiliation', 'affiliation');
            $table->dropColumn('new_affiliation_request');
            $table->dropColumn('approved_affiliation');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('user_notifications', function(Blueprint $table) {
            $table->renameColumn('affiliation', 'new_affiliation');
            $table->boolean('new_affiliation_request')->default(true);
            $table->boolean('approved_affiliation')->default(true);
        });
    }
}
