<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Permission;

class InsertIntoPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Insert the permission into the database
         */
        Permission::create([
            'name' => 'login_sirius_by_manager',
            'title' => 'Acessar o Sirius pelo Manager',
            'guard' => 'web'
        ]);

        /**
         * Assign permission to all Manager users
         */
        $managerUsers = User::where('email', 'like', '%@cloudfox.net')->get();

        foreach($managerUsers as $user) {
            $user->givePermissionTo('login_sirius_by_manager');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where('name', 'login_sirius_by_manager')->delete();
    }
}
