<?php

use Illuminate\Database\Migrations\Migration;
use Modules\Core\Entities\User;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\UserService;

class UpdateIdwallResultInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (env("APP_ENV") == "production") {
            $userService = new UserService();
            $companyService = new CompanyService();

            $users = User::whereRaw("account_owner_id = id")
                ->whereRaw("created_at > DATE_SUB(now(), INTERVAL 2 MONTH)")
                ->get();

            foreach ($users as $user) {
                if (empty($user->id_wall_result)) {
                    $userIdwall = $userService->getUserByIdwallCPF($user->document);

                    if ($userIdwall) {
                        $userJson = json_encode($userIdwall);
                        $user->update(["id_wall_result" => $userJson]);
                    }
                }

                foreach ($user->companies as $company) {
                    if ($company->company_type == 2 && empty($company->id_wall_result)) {
                        $companyIdwall = $companyService->getCompanyByIdwallCNPJ($company->company_document);

                        if ($companyIdwall) {
                            $companyJson = json_encode($companyIdwall);
                            $company->update(["id_wall_result" => $companyJson]);
                        }
                    }
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
}
