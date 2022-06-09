<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Permission;

class InsertNewPermissionIntoTable extends Migration
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
            'name' => 'extract_reports',
            'title' => 'Extração de Relatórios',
            'guard' => 'web'
        ]);

        /**
         * Assign permission to all Manager users
         */
        $managerUsers = User::where('email', 'like', '%@cloudfox.net')->get();

        foreach($managerUsers as $user) {
            $user->givePermissionTo('extract_reports');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Permission::where('name', 'extract_reports')->delete();
    }
}
