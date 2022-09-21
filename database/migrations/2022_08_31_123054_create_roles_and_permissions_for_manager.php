<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
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
        $roleManagerAdmin = Role::create([
            'name' => 'admin',
            'guard_name'=>'manager'
        ]);

        $roleManagerAttendance = Role::create([
            'name' => 'attendance',
            'guard_name'=>'manager'
        ]);

        $roleDE = Role::findByName('document_evaluation');
        $roleDE->update([
            'guard_name'=>'manager'
        ]);

        $roleAA = Role::findByName('antifraud_analysis');
        $roleAA->update([
            'guard_name'=>'manager'
        ]);

        DB::statement("UPDATE permissions SET guard_name = 'manager' WHERE `name` = 'login_sirius_by_manager'");
        DB::statement("UPDATE permissions SET guard_name = 'manager' WHERE `name` = 'extract_reports'");

        $roleAdmin = Role::findByName('admin');
        $roleAttendance = Role::findByName('admin');

        $users = User::where('email','like','%cloudfox.net')
                ->whereNull("account_owner_id")
                ->whereHas("roles", function ($query) {
                    $query->whereIn("name", ["admin", "attendance"]);
                })->get();

        foreach ($users as $user) {
            DB::statement("UPDATE model_has_roles set role_id = {$roleManagerAdmin->id} WHERE model_id = {$user->id} AND role_id = {$roleAdmin->id}");
            DB::statement("UPDATE model_has_roles set role_id = {$roleManagerAttendance->id} WHERE model_id = {$user->id} AND role_id = {$roleAttendance->id}");
        }
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
