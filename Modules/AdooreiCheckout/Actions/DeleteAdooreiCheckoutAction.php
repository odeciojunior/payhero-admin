<?php

declare(strict_types=1);

namespace Modules\AdooreiCheckout\Actions;

use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Webhook;
use Modules\Integrations\Actions\DeleteApiTokenAction;
use Modules\Integrations\Exceptions\ApiTokenNotFoundException;
use Modules\Integrations\Exceptions\UnauthorizedApiTokenDeletionException;

class DeleteAdooreiCheckoutAction
{
    private string $descriptionCheckout = 'Adoorei_Checkout';

    public function __construct(
        private readonly Webhook $webhookModel,
        private readonly DeleteApiTokenAction $deleteApiTokenAction,
    ) {
    }

    /**
     * @throws UnauthorizedApiTokenDeletionException
     * @throws PresenterException
     * @throws ApiTokenNotFoundException
     */
    public function handle(int $apiTokenId): void
    {
        $this->deleteApiTokenAction->handle($apiTokenId);

        $this->webhookModel
            ->newQuery()
            ->where('user_id', auth()->user()?->getAccountOwnerId())
            ->where('company_id', auth()->user()->company_default)
            ->where('description', $this->descriptionCheckout)
            ->delete();
    }

}
