<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Exception;

class WebhookService
{
    /**
     * @param $data
     * @param $period
     * @param $schedule
     * @param $maxAttempts
     */
    public function createWebhookConfig($companyId, $projectId, $endPoint, $allProducts = true, $events = ['*'])
    {

    }

    /**
     * @param $webhookId
     */
    public function removeWebhookConfig($webhookId)
    {

    }

    public function createSentWebhook($data, $period, $schedule, $maxAttempts)
    {

    }

    public function removeSentWebhook()
    {

    }
}
