<?php

namespace Modules\Core\Exceptions\Services;

use Exception;
use Throwable;

/**
 * Class ServiceException
 * @package App\Exceptions\Services
 */
class ServiceException extends Exception
{
    /**
     * ServiceException constructor.
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
