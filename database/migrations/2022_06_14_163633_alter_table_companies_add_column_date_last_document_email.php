<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableCompaniesAddColumnDateLastDocumentEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table
                ->dateTime("date_last_document_notification")
                ->nullable()
                ->default(null)
                ->after("contract_document_status");
        });

        Schema::table("users", function (Blueprint $table) {
            $table
                ->dateTime("date_last_document_notification")
                ->nullable()
                ->default(null)
                ->after("personal_document_status");
        });

        DB::statement("UPDATE companies SET date_last_document_notification = now()");

        DB::statement("UPDATE users SET date_last_document_notification = now()");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("companies", function (Blueprint $table) {
            $table->dropColumn(["date_last_document_notification"]);
        });

        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn(["date_last_document_notification"]);
        });
    }
}
