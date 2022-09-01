<?php

namespace Modules\Core\Services\CustomChecks;

use Illuminate\Support\Facades\Queue;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class QueueSizeCheck extends Check
{
    protected array $queues = [
        'high',
        'default',
        'low',
        'long',
        'postback',
        'postback-shopify-tracking'
    ];

    protected int $maxSize = 10000;

    public function maxSize($maxSize)
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    public function run(): Result
    {
        $currentQueueSize = 0;
        foreach($this->queues as $queueName) {
            $currentQueueSize += Queue::size($queueName);
        }

        $result = Result::make();

        if ($currentQueueSize > $this->maxSize) {
            return $result->failed('The queue size was expected to be lower than '.$this->maxSize.', but actually was ' . $currentQueueSize);
        }
        return $result->ok('Current queue size: ' . $currentQueueSize);
    }
}
