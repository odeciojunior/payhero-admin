<?php

declare(strict_types=1);

namespace Modules\Integrations\Actions;

use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\ApiToken;
use Modules\Integrations\Exceptions\ApiTokenNotFoundException;
use Modules\Integrations\Exceptions\UnauthorizedApiTokenDeletionException;
use Modules\Projects\Actions\DeleteProjectWithCheckoutIntegrationAction;

class DeleteApiTokenAction
{
    public function __construct(
        private readonly ApiToken $apiTokenModel,
        private readonly DeleteProjectWithCheckoutIntegrationAction $deleteProject,
    ) {
    }

    /**
     * @throws UnauthorizedApiTokenDeletionException
     * @throws ApiTokenNotFoundException
     * @throws PresenterException
     */
    public function handle(int $apiTokenId): void
    {
        /** @var ApiToken $apiToken */
        $apiToken = $this->apiTokenModel
            ->newQuery()
            ->where('id', $apiTokenId)
            ->first();

        if (is_null($apiToken)) {
            throw new ApiTokenNotFoundException('O token solicitado não foi encontrado.');
        }

        if ($apiToken->user_id !== auth()->user()?->getAccountOwnerId()) {
            throw new UnauthorizedApiTokenDeletionException('Usuário não autorizado a excluir este token.');
        }

        if (!is_null($apiToken->project_id)) {
            $this->deleteProject->handle($apiToken->project_id);
        }

        $apiToken->delete();
    }
}
