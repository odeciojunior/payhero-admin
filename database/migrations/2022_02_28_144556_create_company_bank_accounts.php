<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBankAccount;

class CreateCompanyBankAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->enum('transfer_type',['PIX','TED'])->default('PIX');
            $table->enum('type_key_pix',['CHAVE_ALEATORIA','EMAIL','TELEFONE','CPF','CNPJ'])->default(null);
            $table->string('key_pix')->default(null);
            $table->string('bank',10)->default(null);
            $table->string('agency',10)->default(null);
            $table->string('agency_digit',3)->default(null);
            $table->string('account',10)->default(null);
            $table->string('account_digit',3)->default(null);
            $table->tinyInteger('is_default')->default(0);
            $table->enum('status',['PENDING','VERIFIED','REFUSED']);
            $table->timestamps();
        });

        $companies = DB::table('companies')->where('document','<>','')
        ->whereNotNull('agency')->whereNotNull('bank')->whereNotNull('account')->whereNotNull('account_ddigit')->get();

        $default = true;
        foreach($companies as $company)
        {
            $default = true;
            if($company->has_pix_key){
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
            'status',
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
        Schema::dropIfExists('company_bank_accounts');
    }
}
