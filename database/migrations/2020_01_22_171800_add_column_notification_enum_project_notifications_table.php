<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNotificationEnumProjectNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table('project_notifications', function(Blueprint $table) {
            $table->tinyInteger('notification_enum')->after('event_enum')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('project_notifications', function(Blueprint $table) {
            $table->dropColumn('notification_enum');
        });
    }
}
