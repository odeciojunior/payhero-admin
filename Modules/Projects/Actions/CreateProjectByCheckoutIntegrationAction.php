<?php

declare(strict_types=1);

namespace Modules\Projects\Actions;

use JsonException;
use Modules\Core\Entities\ApiToken;
use Modules\Core\Entities\Domain;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\UserProject;
use Modules\Core\Services\ProjectNotificationService;

class CreateProjectByCheckoutIntegrationAction
{
    private array $platforms = [
        ApiToken::PLATFORM_ENUM_ADOOREI_CHECKOUT,
        ApiToken::PLATFORM_ENUM_VEGA_CHECKOUT,
    ];

    public function __construct(
        private readonly Project $projectModel,
        private readonly UserProject $userProjectModel,
        private readonly Domain $domainModel,
        private readonly ProjectNotificationService $projectNotificationService,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function handle(array $data): ?Project
    {
        if (!in_array($data['platform_enum'], $this->platforms, true)) {
            return null;
        }

        /**
         * @var Project $projectCreated
         */
        $projectCreated = $this->projectModel
            ->newQuery()
            ->create([
                'name' => $data['name'],
                'description' => $data['name'],
                'visibility' => 'private',
                'automatic_affiliation' => false,
                'status' => Project::STATUS_ACTIVE,
                'notazz_configs' => json_encode([
                    'cost_currency_type' => 1,
                ], JSON_THROW_ON_ERROR),
            ]);

        $this->domainModel
            ->newQuery()
            ->create([
                'project_id' => $projectCreated->id,
                'cloudflare_domain_id' => null,
                'name' => 'pag.net.br',
                'status' => Domain::STATUS_APPROVED,
                'sendgrid_id' => null,
            ]);

        $this->userProjectModel
            ->newQuery()
            ->create([
                'user_id' => auth()->user()?->getAccountOwnerId(),
                'project_id' => $projectCreated->id,
                'company_id' => $data['company_id'],
                'type' => 'producer',
                'type_enum' => UserProject::TYPE_PRODUCER_ENUM,
                'access_permission' => true,
                'edit_permission' => true,
                'status' => 'active',
                'status_flag' => UserProject::STATUS_FLAG_ACTIVE,
            ]);

        $this->projectNotificationService->createProjectNotificationDefault($projectCreated->id);

        return $projectCreated;
    }
}
