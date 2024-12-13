<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\User;
use Modules\Core\Enums\User\UserBiometryStatusEnum;
use Spatie\Permission\Models\Role;

/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        //usuario admin
        $user = User::create([
            "name" => "first admin user",
            "email" => "acesso@azcend.com.br",
            "password" => bcrypt("azcend"),
            "document" => "00000000000",
            "is_cloudfox" => true,
            "cellphone" => "5566999999999",
        ]);

        $role = Role::where("name", "admin")
            ->where("guard_name", "manager")
            ->first();

        $permissions = $role->permissions;

        DB::statement("INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`)
        VALUES ({$role->id}, 'Modules\\\Core\\\Entities\\\User', {$user->id});");

        foreach ($permissions as $permission) {
            DB::statement("INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`)
            VALUES({$permission->id}, 'Modules\\\Core\\\Entities\\\User', {$user->id});");
        }

        //usuario seller
        $userStore = User::create([
            "name" => "Seller Teste",
            "email" => "seller@azcend.com.br",
            "password" => bcrypt("azcend"),
            "document" => "00000000000",
            "account_is_approved" => true,
            "cellphone" => "5566999999999",
            "account_is_approved" => true,
            "personal_document_status" => 3,
            "biometry_status" => UserBiometryStatusEnum::APPROVED->value,
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
