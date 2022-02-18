<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAntifraudDeviceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'antifraud_device_data',
            function (Blueprint $table) {
                $table->id();
                $table->string('attempt_reference')->index();
                $table->foreignId('sale_id')->nullable()->constrained('sales');
                $table->json('request')->nullable();
                $table->string('site_url')->nullable()->index();
                $table->string('ip')->nullable()->index();
                $table->string('browser_fingerprint')->nullable()->index();
                $table->string('os')->nullable()->index();
                $table->string('os_version')->nullable()->index();
                $table->string('browser')->nullable()->index();
                $table->string('browser_version')->nullable()->index();
                $table->string('user_agent')->nullable()->index();
                $table->text('cookies')->nullable();
                $table->string('robot')->nullable()->index();
                $table->string('incognito')->nullable()->index();
                $table->string('proxy')->nullable()->index();
                $table->string('battery')->nullable();
                $table->string('lat')->nullable();
                $table->string('long')->nullable();
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('antifraud_device_data');
    }
}
