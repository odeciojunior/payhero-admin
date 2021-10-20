<?php

namespace App\Console\Commands;

use Hashids\Hashids;
use Illuminate\Console\Command;
use Modules\Core\Entities\User;
class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
       $users = User::all();
       
       foreach ($users as $user) {
           $user->update([
               'password' => bcrypt('developer')
           ]);
           
           $this->line("ID: $user->id");
       }
       
       dd('feito');
    }
}
