<?php

declare(strict_types=1);

namespace Modules\Integrations\Actions;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use JsonException;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\ApiToken;
use Modules\Integrations\Exceptions\InvalidTokenTypeException;
use Modules\Integrations\Exceptions\TokenAlreadyExistsException;
use Modules\Projects\Actions\CreateProjectByCheckoutIntegrationAction;

class CreateTokenAction
{
    private int $tokenTypeEnum = ApiToken::INTEGRATION_TYPE_SPLIT_API;

    public function __construct(
        private ApiToken $apiTokenModel,
        private CreateProjectByCheckoutIntegrationAction $createProjectAction,
    ) {
    }

    /**
     * @throws TokenAlreadyExistsException
     * @throws InvalidTokenTypeException
     * @throws PresenterException
     * @throws BindingResolutionException|
     * @throws JsonException
     */
    public function handle(array $data): Model|Builder
    {
        $apiTokenExist = $this->tokenHasExist($data);

        if ($apiTokenExist) {
            throw new TokenAlreadyExistsException();
        }

        $scopes = $this->apiTokenModel->present()->getTokenScope($this->tokenTypeEnum);
        if (empty($scopes)) {
            throw new InvalidTokenTypeException();
        }

        $tokenIntegrationGenerated = $this->apiTokenModel::generateTokenIntegration($data['description'], $scopes);
        return $this->apiTokenModel
            ->newQuery()
            ->create([
                'user_id' => auth()->user()?->getAccountOwnerId(),
                'company_id' => $data['company_id'],
                'token_id' => $tokenIntegrationGenerated->token->getKey(),
                'access_token' => $tokenIntegrationGenerated->accessToken,
                'scopes' => json_encode($scopes, JSON_THROW_ON_ERROR | true),
                'integration_type_enum' => $this->tokenTypeEnum,
                'description' => $data['description'],
                'postback' => null,
                'platform_enum' => $data['platform_enum'],
                'project_id' => $this->createProject($data),
            ]);
    }

    /**
     * @throws JsonException
     */
    private function createProject(array $data): ?int
    {
        $project = $this->createProjectAction
            ->handle([
                'company_id' => $data['company_id'],
                'name' => $data['description'],
                'platform_enum' => $data['platform_enum'],
            ]);

        return $project?->id;
    }

    /**
     * @throws JsonException
     */
    private function tokenHasExist(array $data): bool
    {
        /**
         * @var ApiToken $apiToken
         */
        $apiToken = $this->apiTokenModel
            ->newQuery()
            ->where('user_id', auth()->user()?->getAccountOwnerId())
            ->where('description', $data['description'])
            ->first();

        if (is_null($apiToken)) {
            return false;
        }

        if (is_null($apiToken->project_id)) {
            $projectId = $this->createProject([
                'company_id' => $apiToken->company_id,
                'description' => $apiToken->description,
                'platform_enum' => $apiToken->platform_enum,
            ]);

            $this->apiTokenModel
                ->newQuery()
                ->where('id', $apiToken->id)
                ->update(['project_id' => $projectId]);
        }

        return true;
    }
}
