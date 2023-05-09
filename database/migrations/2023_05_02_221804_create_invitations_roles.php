<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = now();

        foreach ($this->getNewPermissions() as $permission) {
            $sql = "INSERT INTO permissions (name, title, guard_name, created_at, updated_at) ";
            $sql .= "VALUES('{$permission[0]}', '{$permission[1]}', '{$permission[2]}', '{$now}', '{$now}')";
            DB::select($sql);
        }

        $roles = Role::all();
        foreach ($roles as $role) {
            if ($role->guard_name == "web") {
                switch ($role->name) {
                    case "account_owner":
                    case "admin":
                        $role->syncPermissions([
                            "dashboard",
                            "sales",
                            "sales_manage",
                            "recovery",
                            "trackings",
                            "trackings_manage",
                            "contestations",
                            "contestations_manage",
                            "projects",
                            "projects_manage",
                            "products",
                            "products_manage",
                            "attendance",
                            "attendance_manage",
                            "finances",
                            "finances_manage",
                            "reports",
                            "apps",
                            "apps_manage",
                            "dev",
                            "dev_manage",
                            "invitations",
                            "invitations_manage",
                            "affiliates",
                            "affiliates_manage",
                        ]);
                        break;
                    case "attendance":
                        $role->syncPermissions(["sales", "recovery", "attendance", "attendance_manage"]);
                        break;
                    case "finantial":
                        $role->syncPermissions(["dashboard", "sales", "finances", "finances_manage", "reports"]);
                        break;
                    case "custom":
                        break;
                }
            }
        }
    }

    public function down()
    {
    }

    public function getNewPermissions()
    {
        return [
            ["trackings", "Rastreamento", "web"],
            ["trackings_manage", "Rastreamento - Gerenciar", "web"],
            ["contestations", "Contestações", "web"],
            ["contestations_manage", "Contestações - Gerenciar", "web"],
            ["invitations", "Convites", "web"],
            ["invitations_manage", "Convites - Gerenciar", "web"],
            ["affiliates", "Afiliados", "web"],
            ["affiliates_manage", "Afiliados Gerenciar", "web"],
        ];
    }

    public function getDefaultRoles()
    {
        return [
            ["account_owner", "web"],
            ["admin", "web"],
            ["attendance", "web"],
            ["finantial", "web"],
            ["custom", "web"],
            ["admin", "manager"],
            ["attendance", "manager"],
            ["financial", "manager"],
            ["document_evaluation", "manager"],
            ["antifraud_analysis", "manager"],
        ];
    }
};
