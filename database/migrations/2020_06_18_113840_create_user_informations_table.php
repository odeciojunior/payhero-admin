<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUserInformationsTable
 */
class CreateUserInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("user_informations", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("user_id")->index();
            $table->string("sex", 50)->nullable();
            $table->unsignedInteger("marital_status")->nullable();
            $table->string("nationality", 2)->nullable();
            $table->string("mother_name", 255)->nullable();
            $table->string("father_name", 255)->nullable();
            $table->string("spouse_name", 255)->nullable();
            $table->string("birth_place", 255)->nullable();
            $table->string("birth_city", 255)->nullable();
            $table->string("birth_state", 255)->nullable();
            $table->string("birth_country", 255)->nullable();
            $table->unsignedInteger("monthly_income")->nullable();

            $table->unsignedInteger("document_type")->nullable();
            $table->string("document_number", 255)->nullable();
            $table->dateTime("document_issue_date")->nullable();
            $table->dateTime("document_expiration_date")->nullable();
            $table->string("document_issuer")->nullable();
            $table->string("document_issuer_state")->nullable();
            $table->string("document_serial_number")->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table("user_informations", function (Blueprint $table) {
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("user_informations");
    }
}
