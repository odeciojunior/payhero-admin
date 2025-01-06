<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Entities\CheckoutConfig;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\PixelConfig;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\ProjectUpsellConfig;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\User;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectNotificationService;

class DemoProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $project = Project::create([
            "visibility" => "private",
            "status" => 1,
            "photo" =>
                "https://cloudfox-digital-products.s3.amazonaws.com/uploads/user/NePyE2ZyMZqRbV4/public/project/jeApQgzQqRGEb76/main/TZbzIATUmMLrYykMSquMYHyWfaRgcnPTXGUBMwa8.png",
            "status" => 1,
            "name" => "Primeira loja",
            "description" => "https://first-store.azcend.com.br",
            "reviews_config_icon_type" => "star",
            "reviews_config_icon_color" => "#f8ce1c",
            "notazz_configs" => '{"cost_currency_type": 1}',
        ]);

        UserProject::create([
            "user_id" => User::DEMO_ID,
            "project_id" => $project->id,
            "company_id" => Company::DEMO_ID,
            "status_flag" => 1,
            "status" => "active",
        ]);

        $this->createConfigDefault($project->id);
    }

    public function createConfigDefault($projectId)
    {
        $projectNotificationService = new ProjectNotificationService();
        $projectNotificationService->createProjectNotificationDefault($projectId);

        Domain::create([
            "project_id" => $projectId,
            "name" => "lojista.com.br",
            "status" => Domain::STATUS_APPROVED,
        ]);

        CheckoutConfig::create([
            "project_id" => $projectId,
            "company_id" => Company::DEMO_ID,
        ]);

        PixelConfig::create(["project_id" => $projectId]);

        Shipping::create([
            "project_id" => $projectId,
            "name" => "Frete gratis",
            "information" => "de 15 até 30 dias",
            "value" => 0,
            "type" => "static",
            "type_enum" => 1,
            "status" => "1",
            "pre_selected" => "1",
            "apply_on_plans" => '["all"]',
            "not_apply_on_plans" => "[]",
        ]);

        ProjectUpsellConfig::create([
            "project_id" => $projectId,
            "header" => "(Mensagem do cabeçalho) Ex: Você não pode perder essa promoção...",
            "countdown_time" => null,
            "countdown_flag" => 0,
        ]);
    }
}
