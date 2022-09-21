<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = Role::where('guard_name','manager')->get()->pluck('id');

        $permissions = Permission::where('guard_name','web')->get()->pluck('id');

        $rolesPermissions = DB::table('role_has_permissions')->whereIn('role_id',$roles)->get();

        foreach ($rolesPermissions as $roleP) {
            if(in_array($roleP->permission_id,$permissions->toArray())){
                DB::statement("DELETE FROM role_has_permissions WHERE role_id = {$roleP->role_id} AND permission_id = {$roleP->permission_id}");
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
