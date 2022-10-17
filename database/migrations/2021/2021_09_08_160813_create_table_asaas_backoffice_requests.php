<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAsaasBackofficeRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("asaas_backoffice_requests", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("company_id");
            $table->json("sent_data")->nullable();
            $table->json("response")->nullable();
            $table->timestamps();
            $table
                ->foreign("company_id")
                ->references("id")
                ->on("companies");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("asaas_backoffice_requests");
    }
}
