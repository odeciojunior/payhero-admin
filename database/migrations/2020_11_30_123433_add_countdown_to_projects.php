<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountdownToProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('countdown_timer_flag')->after('whatsapp_button')->nullable();
            $table->string('countdown_timer_color', 7)->after('countdown_timer_flag')->default('#f78d1e')->nullable();
            $table->integer('countdown_timer_time')->after('countdown_timer_color')->nullable();
            $table->string('countdown_timer_description', 255)->after('countdown_timer_time')->nullable();
            $table->string('countdown_timer_finished_message', 255)->after('countdown_timer_description')->default('Seu tempo acabou! Você precisa finalizar sua compra imediatamente.')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('countdown_timer_flag');
            $table->dropColumn('countdown_timer_color');
            $table->dropColumn('countdown_timer_time');
            $table->dropColumn('countdown_timer_description');
            $table->dropColumn('countdown_timer_finished_message');
        });
    }
}
