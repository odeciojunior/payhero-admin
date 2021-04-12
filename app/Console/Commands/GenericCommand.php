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
            $company->update(['subseller_getnet_id' => '700051332', '$subseller_getnet_homolog_id' => '700051332']);
            $this->line("id: " . $company->id);
        }

//        $user = User::find(30);
//        $user->update([
//            'name' => 'Meu nome',
//            'email' => 'teste@email.com',
//            'password' => bcrypt('password'),
//        ]);
//        $this->line("id: " . $user->id);

        dd('feitoo');
    }
}



// SELECT table_schema "Database", ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) "Size(MB)" FROM information_schema.tables GROUP BY table_schema;
