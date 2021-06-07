<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id')->index()->nullable();
            $table->json('postback_data');
            $table->text('onesignal_response')->nullable();
            $table->unsignedTinyInteger('processed_flag')->index()->default(0); // 0-no , 1-yes
            $table->unsignedTinyInteger('postback_valid_flag')->index()->default(0); // 0-no , 1-yes
            $table->json('machine_result')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('push_notifications');
    }
}
