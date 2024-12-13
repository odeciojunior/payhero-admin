<?php

declare(strict_types=1);

namespace Modules\Webhooks\Actions\CreateWebhook;

use Modules\Core\ValueObjects\Url;
use Spatie\LaravelData\Data;

class CreateWebhookInputDTO extends Data
{
    public function __construct(
        public int $userId,
        public int $companyId,
        public string $description,
        public Url $url,
    ) {
    }
}
