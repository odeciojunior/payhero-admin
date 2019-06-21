<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCompanyDocuments extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('company_documents', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('company_id')->index();
            $table->string('document_url', 500);
            $table->tinyInteger('document_type_enum');
            $table->tinyInteger('status')->nullable();

            $table->timestamps();
        });

        Schema::table('company_documents', function(Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('company_documents', function(Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::dropIfExists('company_documents');
    }
}
