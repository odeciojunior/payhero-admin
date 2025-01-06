<?php

declare(strict_types=1);

namespace Modules\Projects\Actions;

use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Project;
use Modules\Core\Services\DomainService;

class DeleteProjectWithCheckoutIntegrationAction
{
    public function __construct(
        private readonly Project $project,
        private readonly DomainService $domainService,
    ) {
    }


    /**
     * @throws PresenterException
     */
    public function handle(int $projectId): void
    {
        /**
         * @var Project $project
         */
        $project = $this->project
            ->with(['notifications', 'domains'])
            ->where('id', $projectId)
            ->first();

        foreach ($project->domains as $domain) {
            $this->domainService->deleteDomain($domain);
        }

        if (!empty($project->notifications) && $project->notifications->isNotEmpty()) {
            foreach ($project->notifications as $notification) {
                $notification->delete();
            }
        }

        $project->update([
            "name" => sprintf('%s (Excluído)', $project->name),
            "status" => $this->project->present()->getStatus('disabled'),
        ]);
    }

}
