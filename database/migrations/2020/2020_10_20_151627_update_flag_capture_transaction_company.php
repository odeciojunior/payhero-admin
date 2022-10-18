<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\Company;

class UpdateFlagCaptureTransactionCompany extends Migration
{
    public function up()
    {
        $companyModel = new Company();
        $companies = $companyModel->all();

        foreach ($companies as $company) {
            if ($company->get_net_status == $companyModel->present()->getStatusGetnet("approved")) {
                $company->update([
                    "capture_transaction_enabled" => true,
                ]);
            } else {
                $company->update([
                    "capture_transaction_enabled" => false,
                ]);
            }
        }
    }

    public function down()
    {
        //
    }
}
