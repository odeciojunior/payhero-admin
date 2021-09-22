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
        $this->rollbackPermissionAdmin();
    }

    public function setPermissionsAdmin(){
        $users = User::role('account_owner')->get();
        foreach($users as $us){
            $us->syncRoles(['account_owner','admin']);
        }
    }
   
    public function rollbackPermissionAdmin(){
        $users = DB::select("SELECT COUNT(model_id) as total, model_id FROM model_has_roles WHERE role_id = 4 OR role_id = 5 GROUP BY model_id;");
        
        $total = count($users);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach($users as $us){
            if($us->total >= 2){
                $user = User::find($us->model_id);
                $user->syncRoles(['account_owner']);
                $this->line('User id: ' .  $us->model_id );
            }
            $bar->advance();
        }
        $bar->finish();
        
    }
}
