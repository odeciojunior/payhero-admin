<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;

class UpdateAccountIsApprovedInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (User::all() as $user) {
            if ($user->address_document_status == 3 && $user->personal_document_status == 3) {
                $hasCompanyPfApproved = Company::where("user_id", $user->id)
                    ->where("company_type", 1)
                    ->where("bank_document_status", 3)
                    ->where("capture_transaction_enabled", 1)
                    ->exists();

                $hasCompanyPjApproved = Company::where("user_id", $user->id)
                    ->where("company_type", 2)
                    ->where("address_document_status", 3)
                    ->where("contract_document_status", 3)
                    ->where("bank_document_status", 3)
                    ->where("capture_transaction_enabled", 1)
                    ->exists();

                if ($hasCompanyPjApproved || $hasCompanyPfApproved) {
                    $user->update(["account_is_approved" => true]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
