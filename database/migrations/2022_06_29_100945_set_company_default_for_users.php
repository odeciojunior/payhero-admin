<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class SetCompanyDefaultForUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `users`
        CHANGE COLUMN `company_default` `company_default` INT(10) UNSIGNED NULL DEFAULT '1' AFTER `block_attendance_balance`;");

        $companies = DB::table('companies')->select('id','user_id','address_document_status','contract_document_status')
        ->whereNull('deleted_at')->get();

        $output = new ConsoleOutput();
        $bar = new ProgressBar($output, count($companies));        
        $bar->start();

        $companyDefault = 1;

        foreach ($companies as $company) {
            $bar->advance();
            $companyDefault = 1;
            if($company->address_document_status == Company::DOCUMENT_STATUS_APPROVED && $company->contract_document_status == Company::DOCUMENT_STATUS_APPROVED)
            {
                $companyDefault = $company->id;
            }
            
            User::find($company->user_id)->update([
                'company_default'=>$companyDefault
            ]);
        }

        $bar->finish();

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
