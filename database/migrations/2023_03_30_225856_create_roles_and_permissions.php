<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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

        foreach ($this->getDefaultPermissions() as $permission) {
            $sql = "INSERT INTO permissions (name, title, guard_name, created_at, updated_at) ";
            $sql .= "VALUES('{$permission[0]}', '{$permission[1]}', '{$permission[2]}', '{$now}', '{$now}')";
            DB::select($sql);
        }

        foreach ($this->getDefaultRoles() as $row) {
            $role = Role::create([
                "name" => $row[0],
                "guard_name" => $row[1],
                "created_at" => $now,
                "updated_at" => $now,
            ]);

            if ($row["1"] == "web") {
                switch ($row[0]) {
                    case "account_owner":
                    case "admin":
                        $role->syncPermissions([
                            "dashboard",
                            "sales",
                            "sales_manage",
                            "recovery",
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

            if ($row["1"] == "manager" && $row["0"] == "admin") {
                $permissions = Permission::whereIn("name", ["login_admin_by_manager", "extract_reports"])
                    ->where("guard_name", "manager")
                    ->get();

                foreach ($permissions as $permission) {
                    DB::statement("INSERT INTO role_has_permissions VALUES ({$permission->id},{$role->id})");
                }
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
    }

    public function getDefaultPermissions()
    {
        return [
            ["dashboard", "Dashboard", "web"],
            ["sales", "Vendas", "web"],
            ["sales_manage", "Vendas - Gerenciar", "web"],
            ["recovery", "Recuperação", "web"],
            ["projects", "Lojas", "web"],
            ["projects_manage", "Lojas - Gerenciar", "web"],
            ["products", "Produtos", "web"],
            ["products_manage", "Produtos - Gerenciar", "web"],
            ["attendance", "Atendimento", "web"],
            ["attendance_manage", "Atendimento - Gerenciar", "web"],
            ["finances", "Finanças", "web"],
            ["finances_manage", "Finanças - Gerenciar", "web"],
            ["reports", "Relatórios", "web"],
            ["apps", "Aplicativos", "web"],
            ["apps_manage", "Aplicativos - Gerenciar", "web"],
            ["dev", "Dev", "web"],
            ["dev_manage", "Dev - Gerenciar", "web"],
            ["login_admin_by_manager", "Acessar painel administrativo dos usuários pelo manager", "manager"],
            ["extract_reports", "Extração de relatórios", "manager"],
            ["invitations", "Convites", "web"],
            ["invitations_manage", "Convites - Gerenciar", "web"],
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
