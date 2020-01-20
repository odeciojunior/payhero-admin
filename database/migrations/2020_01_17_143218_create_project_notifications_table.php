<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('project_id')->index();
            $table->boolean('status')->default(1);
            $table->tinyInteger('type_enum');
            $table->tinyInteger('event_enum');
            $table->string('time');
            $table->text('message');
            $table->timestamps();
        });

        Schema::table('project_notifications', function(Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_notifications');
    }
}
