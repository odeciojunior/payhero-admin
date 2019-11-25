<?php

use Modules\Core\Entities\Company;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompanyType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        $companyModel = new Company();

        $companies = $companyModel->all();
        
        foreach($companies as $company) {

            $document = preg_replace("/[^0-9]/", "", $company->company_document);

            if(strlen($document) == 11){
                $company->update([
                    'company_type' => $companyModel->present()->getCompanyType('physical person')
                ]);
            }
            else {
                $company->update([
                    'company_type' => $companyModel->present()->getCompanyType('juridical person')
                ]);
            }
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        $companyModel = new Company();

        $companies = $companyModel->all();
        
        foreach($companies as $company) {

            $company->update([
                'company_type' => null
            ]);
        }

    }
}

