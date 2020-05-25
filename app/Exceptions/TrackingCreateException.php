<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TrackingCreateException extends Exception
{
    public function __construct($message = "Erro ao salvar código de rastreio", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
