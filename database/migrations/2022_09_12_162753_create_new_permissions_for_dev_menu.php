<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, 2);
        $output->writeln("\nCriando novas permissões");
        $progress->start();

        $permission1 = Permission::create([
            'name'=>'dev',
            'title'=>'Dev',
            'guard_name'=>'web'
        ]);
        $progress->advance();

        $permission2 = Permission::create([
            'name'=>'dev_manage',
            'title'=>'Dev - Gerenciar',
            'guard_name'=>'web'
        ]);
        $progress->advance();
        $progress->finish();

        $output->writeln("\nAtribuindo novas permissões");

        $roles = Role::whereIn('name',['account_owner','admin'])->where('guard_name','web')->get();
        $progress = new ProgressBar($output, count($roles));
        $progress->start();

        foreach ($roles as $role)
        {
            DB::statement("INSERT INTO role_has_permissions VALUES($permission1->id, $role->id);");
            DB::statement("INSERT INTO role_has_permissions VALUES($permission2->id, $role->id);");

            $users = User::role($role->name)->get();
            foreach ($users as $user) {
                DB::statement("INSERT INTO model_has_permissions VALUES($permission1->id,'Modules\\Core\\Entities\\User',$user->id);");
                DB::statement("INSERT INTO model_has_permissions VALUES($permission2->id,'Modules\\Core\\Entities\\User',$user->id);");
            }
            $progress->advance();
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

    }
};
