<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'user_informations',
            function (Blueprint $table) {

                $table->unsignedInteger('patrimony')->nullable()->after('working_end_time');
                $table->string('state_fiscal_document_number', 255)->nullable()->after('monthly_gross_income');
                $table->string('business_entity_type', 255)->nullable()->after('state_fiscal_document_number');
                $table->string('economic_activity_classification_code', 255)->nullable()->after('business_entity_type');
                $table->unsignedInteger('monthly_gross_income')->nullable()->after('order_priority');
                $table->unsignedInteger('federal_registration_status')->nullable()->after('monthly_gross_income');
                $table->date('founding_date')->nullable()->after('federal_registration_status');
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
        //
    }
}
