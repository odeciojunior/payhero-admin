<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InternalApiAuth
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $apiName  = $request->header('Api-name') ?? null;
        $apiToken = $request->header('Api-token') ?? null;
        if ($apiName && $apiToken) {
            if ($apiToken != $this->getTokenApi($apiName)) {
                return response()->json('Token de acesso inválido!', 401);
            }
        } else {
            return response()->json('Token de acesso requerido!', 401);
        }

        return $next($request);
    }

    public function getTokenApi(string $apiName)
    {
        $tokenName = strtoupper($apiName) . '_TOKEN';

        return env($tokenName);
    }
}
