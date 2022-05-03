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
            $table->enum('type_key_pix',['CHAVE_ALEATORIA','EMAIL','TELEFONE','CPF','CNPJ'])->nullable()->default(null);
            $table->string('key_pix')->nullable()->default(null);
            $table->string('bank',10)->nullable()->default(null);
            $table->string('agency',10)->nullable()->default(null);
            $table->string('agency_digit',3)->nullable()->default(null);
            $table->string('account',10)->nullable()->default(null);
            $table->string('account_digit',3)->nullable()->default(null);
            $table->tinyInteger('is_default')->default(0);
            $table->enum('status',['PENDING','VALIDATING','VERIFIED','REFUSED']);
            $table->string('gateway_transaction_id')->nullable()->default(null);;            
            $table->timestamps();
            $table->softDeletes();
        });
        
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
