<?php

namespace Modules\Core\Services\CustomChecks;

use Illuminate\Support\Facades\Queue;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class QueueSizeCheck extends Check
{
    protected int $maxSize = 10000;

    public function maxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    public function run(): Result
    {
        $currentQueueSize = Queue::size();

        $result = Result::make();

        if ($currentQueueSize > $this->maxSize) {
            return $result->failed('The queue size was expected to be lower than '.$this->maxSize.', but actually was ' . $currentQueueSize);
        }
        return $result->ok('Current queue size: ' . $currentQueueSize);
    }
}
