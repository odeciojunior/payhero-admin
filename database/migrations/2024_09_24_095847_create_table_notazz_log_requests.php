<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notazz_log_requests', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger('nf_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->json("request");
            $table->json("response");
            $table->timestamp("created_at")->useCurrent();
            $table->timestamp("updated_at")->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notazz_log_requests');
    }
};
