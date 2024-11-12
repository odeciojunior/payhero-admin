<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleMonitorTables extends Migration
{
    public function up()
    {
        // // Drop tables if they exist
        // Schema::dropIfExists('scheduled_task_logs');
        // Schema::dropIfExists('monitored_scheduled_tasks');

        // // Create the monitored_scheduled_tasks table
        // Schema::create('monitored_scheduled_tasks', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('name');
        //     $table->string('type')->nullable();
        //     $table->string('cron_expression');
        //     $table->string('timezone')->nullable();
        //     $table->string('ping_url')->nullable();
        //     $table->dateTime('last_started_at')->nullable();
        //     $table->dateTime('last_finished_at')->nullable();
        //     $table->dateTime('last_failed_at')->nullable();
        //     $table->dateTime('last_skipped_at')->nullable();
        //     $table->timestamps();
        // });

        // // Create the scheduled_task_logs table
        // Schema::create('scheduled_task_logs', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->unsignedBigInteger('task_id');
        //     $table->foreign('task_id')->references('id')->on('monitored_scheduled_tasks')->onDelete('cascade');
        //     $table->dateTime('started_at')->nullable();
        //     $table->dateTime('finished_at')->nullable();
        //     $table->dateTime('failed_at')->nullable();
        //     $table->dateTime('skipped_at')->nullable();
        //     $table->text('output')->nullable();
        //     $table->timestamps();
        // });
    }

    public function down()
    {
        // // Drop tables if they exist
        // Schema::dropIfExists('scheduled_task_logs');
        // Schema::dropIfExists('monitored_scheduled_tasks');
    }
}