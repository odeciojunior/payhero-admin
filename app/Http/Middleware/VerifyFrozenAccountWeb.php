<?php

namespace App\Http\Middleware;

use Closure;
use Modules\Core\Entities\User;

class VerifyFrozenAccountWeb
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
        if((auth()->user()->status ?? null) == (new User)->present()->getStatus('account frozen') && 
            $this->inExceptArray($request) == false) {

            return response()->redirectTo('/dashboard')->withErrors(['accountErrors' => 'Conta congelada']);
        }

        return $next($request);
    }


    protected function inExceptArray($request)
    {   
        $excepts = [
            '/dashboard',
            '/sales',
            '/recovery',
            '/trackings',
            '/projects',
            '/products',
            '/attendance',
            '/finances',
            '/old-finances',
            '/reports/sales',
            '/reports/checkouts',
            '/reports/coupons',
            '/reports/pending',
            '/reports/blockedbalance',
            '/affiliates',
            '/apps',
            '/invitations',
            '/logout',
        ];

        foreach ($excepts as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except) || in_array(substr($request->fullUrl(), -4), ['xlsx', '.csv'])) {
                return true;
            }
        }

        return false;
    }

}
