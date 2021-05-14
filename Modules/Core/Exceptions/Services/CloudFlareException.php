<?php

namespace Modules\Core\Exceptions\Services;

use Exception;
use Throwable;

/**
 * Class CloudFlareException
 * @package Modules\Core\Exceptions\Services
 */
class CloudFlareException extends Exception
{
    /**
     * CloudFlareException constructor.
     * @param string $message
     * @param string $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = '0', Throwable $previous = null)
    {
        if (!is_numeric($code)) {
            $code = null;
        }
        // Invoke parent
        parent::__construct($message, $code, $previous);
    }
}
