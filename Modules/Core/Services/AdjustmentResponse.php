<?php

namespace Modules\Core\Services;

class AdjustmentResponse
{
    public ?int $code = null;
    public bool $isSuccess;
    public ?string $errorMessage,
        $errorCode = null;
}
