<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\CompanyBankAccount;
use Illuminate\Support\Str;

class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "generic {name?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            
            foreach (CompanyBankAccount::cursor() as $companieBankAccount) {

                if ($companieBankAccount->transfer_type == "PIX") {

                    if (str_contains($companieBankAccount->key_pix, "@")) {
                        $companieBankAccount->type_key_pix = "EMAIL";
                    } elseif (str_contains($companieBankAccount->key_pix, "-") and (strlen($companieBankAccount->key_pix) == 36)) {
                        $companieBankAccount->type_key_pix = "CHAVE_ALEATORIA";
                    } elseif (str_contains($companieBankAccount->key_pix, "/") and (strlen($companieBankAccount->key_pix) == 18)) {
                        $companieBankAccount->type_key_pix = "CNPJ";
                    } elseif (str_contains($companieBankAccount->key_pix, ".") and str_contains($companieBankAccount->key_pix, "-") and (strlen($companieBankAccount->key_pix) == 14)) {
                        $companieBankAccount->type_key_pix = "CNPJ";
                    } elseif (strlen($companieBankAccount->key_pix) == strlen(foxutils()->onlyNumbers($companieBankAccount->key_pix))) {
                        $companieBankAccount->type_key_pix = "TELEFONE";
                    } else {
                        $this->line("id: " . $companieBankAccount->id . " - " . $companieBankAccount->key_pix);
                    }

                    $companieBankAccount->save();

                    if (isset($companieBankAccount->type_key_pix) && in_array($companieBankAccount->type_key_pix, ['TELEFONE', 'CPF', 'CNPJ'])) {
                        $companieBankAccount->key_pix = foxutils()->onlyNumbers($companieBankAccount->key_pix);
                        //$this->line("id: " . $companieBankAccount->id . " - " . $companieBankAccount->key_pix);
                        $companieBankAccount->save();
                    }
                }
            }
        } catch (Exception $ex) {
            report($ex->getMessage());
        }
    }
}
