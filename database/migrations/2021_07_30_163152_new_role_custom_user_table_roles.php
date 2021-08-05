<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NewRoleCustomUserTableRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(['name' => 'custom']);        

        $userAdmin = User::create(
            [
                'name'           => "Manager custom",
                'email'          => "custom@cloudfox.net",
                'email_verified' => "1",
                'password'       => bcrypt('P0Rj7mvP0RIO@&F^mPLX#T'),
            ]
        );
        
        $userAdmin->assignRole('custom');
        
        Permission::create(['name'=>'dashboard']);
        Permission::create(['name'=>'sales']);
        Permission::create(['name'=>'sales_reverse_transaction']);
        Permission::create(['name'=>'recovery']);
        Permission::create(['name'=>'sales_recovery']);
        Permission::create(['name'=>'trackings']);
        Permission::create(['name'=>'sales_trackings']);
        Permission::create(['name'=>'sales_trackings_manager']);
        Permission::create(['name'=>'contestations']);
        Permission::create(['name'=>'sales_contestations']);
        Permission::create(['name'=>'sales_contestations_send_documents']);
        Permission::create(['name'=>'projects']);
        Permission::create(['name'=>'projects_manage']);
        Permission::create(['name'=>'products']);
        Permission::create(['name'=>'products_manage']);
        Permission::create(['name'=>'attendance']);
        Permission::create(['name'=>'attendance_manage']);
        Permission::create(['name'=>'finances']);
        Permission::create(['name'=>'finances_manage']);
        Permission::create(['name'=>'report_sales']);
        Permission::create(['name'=>'report_checkouts']);
        Permission::create(['name'=>'report_coupons']);
        Permission::create(['name'=>'report_pending']);
        Permission::create(['name'=>'report_blocked_balance']);
        Permission::create(['name'=>'affialiates']);
        Permission::create(['name'=>'affialiates_manage']);
        Permission::create(['name'=>'apps']);
        Permission::create(['name'=>'apps_manage']);
        Permission::create(['name'=>'invitations']);
        Permission::create(['name'=>'invitations_manage']);
        
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
}
