<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Passport\Exceptions\MissingScopeException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = ["password", "password_confirmation"];

    public function report(Throwable $e)
    {
        if ($e instanceof OAuthServerException && $e->getCode() == 9) {
            return;
        }

        if (app()->bound("sentry") && $this->shouldReport($e)) {
            app("sentry")->captureException($e);
        }

        parent::report($e);
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof HttpException && $e->getStatusCode() === 503) {
            return response()->view("errors.maintenance");
        }

        if ($e instanceof MissingScopeException) {
            return response()->json(["message" => "Acesso não autorizado"], 403);
        }

        return parent::render($request, $e);
    }
}
