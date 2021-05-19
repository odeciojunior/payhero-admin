<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Role;

class UpdateRolesTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $sql = 'DELETE FROM model_has_roles';
        DB::select($sql);

        $sql = 'DELETE FROM roles';
        DB::select($sql);

        Role::create(['name' => 'account_owner']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'attendance']);

        $users = User::all();
        foreach ($users as $user) {
            $user->assignRole('account_owner');
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        //
    }
}
