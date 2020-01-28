<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersDeviceTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('user_devices', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('player_id');
            $table->boolean('online');
            $table->string('identifier')->nullable();
            $table->integer('session_count')->nullable();
            $table->string('language')->nullable();
            $table->integer('timezone')->nullable();
            $table->string('game_version')->nullable();
            $table->string('device_os')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_model')->nullable();
            $table->string('ad_id')->nullable();
            $table->JSON('tags')->nullable();
            $table->integer('last_active')->nullable();
            $table->integer('playtime')->nullable();
            $table->string('amount_spent')->nullable();
            $table->integer('onsignal_created_at')->nullable();
            $table->boolean('invalid_identifier')->nullable();
            $table->integer('badge_count')->nullable();
            $table->string('sdk')->nullable();
            $table->integer('test_type')->nullable();
            $table->string('ip')->nullable();
            $table->string('external_user_id')->nullable();
            $table->boolean('sale_notification');
            $table->boolean('billet_notification');
            $table->boolean('payment_notification');
            $table->boolean('withdraw_notification');
            $table->boolean('invitation_sale_notification');
            $table->timestamps();
        });
        Schema::table('user_devices', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('user_devices', function(Blueprint $table) {
            $table->dropForeign(["user_id"]);
        });
        Schema::dropIfExists('user_devices');
    }
}
