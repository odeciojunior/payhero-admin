<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\User;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Slince\Shopify\Manager\Refund\Refund;
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
        Role::create(["name" => "custom"]);

        $userAdmin = User::create([
            "name" => "Manager custom",
            "email" => "custom@cloudfox.net",
            "email_verified" => "1",
            "password" => bcrypt("P0Rj7mvP0RIO@&F^mPLX#T"),
        ]);

        $userAdmin->assignRole("custom");

        $refund = Permission::find(1);
        $refund->name = "sales_manage";
        $refund->update();

        Permission::create(["name" => "dashboard"]);
        Permission::create(["name" => "sales"]);
        Permission::create(["name" => "recovery"]);
        Permission::create(["name" => "trackings"]);
        Permission::create(["name" => "trackings_manage"]);
        Permission::create(["name" => "contestations"]);
        Permission::create(["name" => "contestations_manage"]);
        Permission::create(["name" => "projects"]);
        Permission::create(["name" => "projects_manage"]);
        Permission::create(["name" => "products"]);
        Permission::create(["name" => "products_manage"]);
        Permission::create(["name" => "attendance"]);
        Permission::create(["name" => "attendance_manage"]);
        Permission::create(["name" => "finances"]);
        Permission::create(["name" => "finances_manage"]);
        Permission::create(["name" => "report_sales"]);
        Permission::create(["name" => "report_checkouts"]);
        Permission::create(["name" => "report_coupons"]);
        Permission::create(["name" => "report_pending"]);
        Permission::create(["name" => "report_blockedbalance"]);
        Permission::create(["name" => "affiliates"]);
        Permission::create(["name" => "affiliates_manage"]);
        Permission::create(["name" => "apps"]);
        Permission::create(["name" => "apps_manage"]);
        Permission::create(["name" => "invitations"]);
        Permission::create(["name" => "invitations_manage"]);
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
