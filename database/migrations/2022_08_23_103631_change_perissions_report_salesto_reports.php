<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class ChangePerissionsReportSalestoReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Permission::where('name','report_sales')->first()->update([
            'name'=>'reports',
            'title'=>'Relatórios'
        ]);

        Permission::whereIn('name',['report_checkouts','report_coupons','report_pending','report_blockedbalance'])->forceDelete();

        Permission::where('id',29)->where('name','login_sirius_by_manager')->forceDelete();

        $roles = Role::all();
        $permissionsList = Permission::whereNotIn('name',['login_sirius_by_manager','extract_reports'])->get()->pluck("name");
        $permissions = [
            "admin" => [],
            "account_owner" => [],
            "attendance" => [],
            "finantial" => [],
            "custom" => [],
            "document_evaluation" => [],
            "antifraud_analysis" => [],
        ];

        /**
         * admin e account_owner tem as mesmas permissões no sirius, porem eles podem diferir em algumas validações feitas por função
         */
        foreach ($roles as $role) {
            switch ($role->name) {
                case "admin": //cloudfox
                    $permissions["admin"] = $permissionsList;
                    unset($permissions["admin"]["invitations_manage"]);
                    $role->syncPermissions($permissions["admin"]);
                    break;
                case "account_owner": //customer
                    $permissions["account_owner"] = $permissionsList;
                    $role->syncPermissions($permissions["account_owner"]);
                    break;
                case "attendance": //customer
                    $permissions["attendance"] = [
                        "sales",
                        "recovery",
                        "trackings",
                        "trackings_manage",
                        "contestations",
                        "contestations_manage",
                        "attendance",
                        "attendance_manage",
                        "reports",
                    ];
                    $role->syncPermissions($permissions["attendance"]);
                    break;
                case "document_evaluation": //cloudfox
                    $permissions["document_evaluation"] = ["finances", "finances_manage"];
                    $role->syncPermissions($permissions["document_evaluation"]);
                    break;
                case "antifraud_analysis": //cloudfox
                    $permissions["antifraud_analysis"] = ["finances", "finances_manage"];
                    $role->syncPermissions($permissions["antifraud_analysis"]);
                    break;
                case "finantial": //customer
                    $permissions["finantial"] = [
                        "dashboard",
                        "sales",
                        "finances",
                        "finances_manage",
                        "reports",
                    ];
                    $role->syncPermissions($permissions["finantial"]);
                    break;
                case "custom": //customer
                    $role->syncPermissions([]);
                    break;
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
}
