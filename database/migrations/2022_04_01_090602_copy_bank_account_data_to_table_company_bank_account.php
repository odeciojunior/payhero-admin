<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBankAccount;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class CopyBankAccountDataToTableCompanyBankAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = DB::table('companies')->where('document','<>','')->where('bank_document_status',true)
        ->whereNotNull('agency')->whereNotNull('bank')->whereNotNull('account')->whereNotNull('account_digit')->get();

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, count($companies));
        $progress->start();
        
        $default = true;
        foreach($companies as $company)
        {    
            $progress->advance();
            try{
                $default = true;
                if($company->has_pix_key && $company->pix_key_situation=='VERIFIED'){
                    CompanyBankAccount::create([
                        'company_id'=>$company->id,
                        'transfer_type'=>'PIX',
                        'type_key_pix'=>$company->company_type==Company::JURIDICAL_PERSON?'CNPJ':'CPF',
                        'key_pix'=>$company->document,
                        'is_default'=>true,
                        'status'=>'VERIFIED',
                    ]);     
                    $default =false;
                }
                                
                CompanyBankAccount::create([
                    'company_id'=>$company->id,
                    'transfer_type'=>'TED',
                    'bank'=>$company->bank,
                    'agency'=>$company->agency,
                    'agency_digit'=>$company->agency_digit,
                    'account'=>$company->account,
                    'account_digit'=>$company->account_digit,
                    'is_default'=>$default,
                    'status'=>'VERIFIED',
                ]); 
            }catch(Exception $e){
                continue;
            }     
        }

        $progress->finish();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       DB::select('TRUNCATE company_bank_account;');
    }
}
