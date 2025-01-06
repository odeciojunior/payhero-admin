<?php

declare(strict_types=1);

namespace Modules\Webhooks\Actions;

use Illuminate\Support\Str;
use JsonException;

class GenerateSignatureWebhookAction
{
    /**
     * @throws JsonException
     */
    public static function handle(array $data): string
    {
        return hash_hmac('sha256', json_encode([
            'user_id' => $data['user_id'],
            'company_id' => $data['company_id'],
            'description' => $data['description'],
            'url' => $data['url']->getUrl(),
            'created_at' => now()->unix(),
        ], JSON_THROW_ON_ERROR), Str::random(32));
    }
}
