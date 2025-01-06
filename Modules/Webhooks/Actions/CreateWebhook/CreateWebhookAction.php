<?php

declare(strict_types=1);

namespace Modules\Webhooks\Actions\CreateWebhook;

use Illuminate\Support\Str;
use JsonException;
use Modules\Core\Entities\Webhook;
use Modules\Webhooks\Actions\GenerateSignatureWebhookAction;

class CreateWebhookAction
{
    public function __construct(
        private readonly Webhook $webhookModel,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function handle(CreateWebhookInputDTO $inputDTO): CreateWebhookOutputDTO
    {
        $existingWebhook = $this->webhookModel
            ->newQuery()
            ->where('user_id', $inputDTO->userId)
            ->where('company_id', $inputDTO->companyId)
            ->where('description', $inputDTO->description)
            ->where('url', $inputDTO->url)
            ->first();

        if ($existingWebhook) {
            /**
             * @var Webhook $existingWebhook
             */
            if (empty($existingWebhook->signature)) {
                $existingWebhook->update([
                    'signature' => GenerateSignatureWebhookAction::handle([
                        'user_id' => $inputDTO->userId,
                        'company_id' => $inputDTO->companyId,
                        'description' => $inputDTO->description,
                        'url' => $inputDTO->url,
                    ]),
                ]);
            }

            return new CreateWebhookOutputDTO($existingWebhook);
        }

        /**
         * @var Webhook $webhook
         */
        $webhook = $this->webhookModel
            ->newQuery()
            ->create([
                'user_id' => $inputDTO->userId,
                'company_id' => $inputDTO->companyId,
                'description' => $inputDTO->description,
                'url' => $inputDTO->url->getUrl(),
                'signature' => GenerateSignatureWebhookAction::handle([
                    'user_id' => $inputDTO->userId,
                    'company_id' => $inputDTO->companyId,
                    'description' => $inputDTO->description,
                    'url' => $inputDTO->url,
                ]),
            ]);

        return new CreateWebhookOutputDTO($webhook);
    }
}
