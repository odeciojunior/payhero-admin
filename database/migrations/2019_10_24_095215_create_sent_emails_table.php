<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentEmailsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('sent_emails', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('from_email', 320);
            $table->string('from_name', 255);
            $table->string('to_email', 320);
            $table->string('to_name', 255);
            $table->string('template_id', 255)->nullable();
            $table->json('template_data')->nullable();
            $table->integer('status_code')->unsigned();
            $table->string('status', 255);
            $table->text('log_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sent_emails');
    }
}
