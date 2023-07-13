<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\User;
use Spatie\Permission\Models\Role;

/**
 * Class UsersTableSeeder
 */
class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        $userStore = User::create([
            "name" => "Lojista Demo",
            "email" => "lojista@demo.com.br",
            "password" => bcrypt("azcend"),
            "document" => "00000000000",
            "account_is_approved" => true,
            "cellphone" => "5511999999999",
            "biometry_status" => 3,
            "address_document_status" => 3,
            "cellphone_verified" => 1,
            "email_verified" => 1,
        ]);

        $userStore->update([
            "account_owner_id" => $userStore->id,
        ]);

        $roleStore = Role::where("name", "admin")
            ->where("guard_name", "web")
            ->first();

        $permissionsStore = $roleStore->permissions;

        $userStore->assignRole("admin");
        $userStore->syncPermissions($permissionsStore);
    }
}
