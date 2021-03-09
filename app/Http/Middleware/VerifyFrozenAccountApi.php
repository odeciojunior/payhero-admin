<?php

namespace App\Http\Middleware;

use Closure;
use Modules\Core\Entities\User;

class VerifyFrozenAccountApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ((auth()->user()->status ?? null) == (new User)->present()->getStatus('account frozen') && 
            $this->inExceptArray($request) == false) {
            return response()->json(['message' => 'Conta congelada!'], 400);
        }

        return $next($request);
    }

    protected function inExceptArray($request)
    {   
        $excepts = [
            '/api/dashboard/getvalues',
            '/api/recovery/details',
            '/api/recovery/export',
            '/api/tracking/export',
            '/api/tracking',
            '/api/tickets/sendmessage',
            '/api/old_finances/export',
            '/api/transfers/account-statement-data/export',
            '/api/logout',
        ];

        if(strtoupper($request->method()) == 'GET') {
            return true;
        }

        $url = substr($request->getRequestUri(), 0, strripos($request->getRequestUri(), '/'));
        if(in_array($url, ['/api/tracking/notify', '/api/withdrawals/get-transactions'])) {
            return true;
        }

        foreach ($excepts as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

}