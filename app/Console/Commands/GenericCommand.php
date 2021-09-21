<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    public function handle()
    {
        $users = User::role('account_owner')->get();
        foreach($users as $us){
            $us->syncRoles(['account_owner','admin']);
        }
    }

    public function rollback(){
        $users = User::role('account_owner')->get();
        foreach($users as $us){
            $us->syncRoles(['account_owner']);
        }
    }
}
