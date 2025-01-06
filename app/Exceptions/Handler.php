<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Passport\Exceptions\MissingScopeException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = ["password", "password_confirmation"];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e); // Ajuste para capturar exceções
            }
        });
    }

    public function report(Throwable $e): void
    {
        if ($e instanceof OAuthServerException && $e->getCode() === 9) {
            return;
        }

        parent::report($e);
    }

    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
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
