<?php

declare(strict_types=1);

namespace Modules\Webhooks\Actions\CreateWebhook;

use Modules\Core\Entities\Webhook;
use Spatie\LaravelData\Data;

class CreateWebhookOutputDTO extends Data
{
    public function __construct(
        public ?Webhook $webhook = null,
    ) {
    }
}
