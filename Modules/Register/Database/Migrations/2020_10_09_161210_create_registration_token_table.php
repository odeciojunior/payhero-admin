<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_token', function (Blueprint $table) {
            $table->id();
            $table->enum('type', array('sms','email'))->default('email');
            $table->string('type_data');
            $table->string('token');
            $table->timestamp('expiration');
            $table->boolean('validated')->default(false);
            $table->unsignedInteger('number_wrong_attempts')->default(0);
            $table->ipAddress('ip');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_token');
    }
}
