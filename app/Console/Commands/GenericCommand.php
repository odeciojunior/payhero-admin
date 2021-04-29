<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Company;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    public function handle()
    {
        foreach(Company::all() as $company) {
            $company->update(['subseller_getnet_id' => '700051332', 'subseller_getnet_homolog_id' => '700051332']);
            $this->line("id: " . $company->id);
        }

        dd('feitoo');
    }
}
