<?php

use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompanyDocumentRemoveMask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companyModel = new Company();

        $companies = $companyModel->all();

        foreach($companies as $company) {

            $company->update([
                'company_document' => preg_replace("/[^0-9]/", "", $company->company_document),
            ]);
        }
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
