<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Modules\Core\Entities\User;

class VerifyFrozenAccountWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (str_starts_with($request->getRequestUri(), "/register/login")) {
            return $next($request);
        }
        if (
            (auth()->user()->status ?? null) === User::STATUS_ACCOUNT_FROZEN &&
            false === $this->inExceptArray($request)
        ) {
            return response()
                ->redirectTo("/dashboard")
                ->withErrors(["accountErrors" => "Conta congelada"]);
        }

        return $next($request);
    }

    protected function inExceptArray($request)
    {
        $excepts = [
            "/dashboard",
            "/sales",
            "/recovery",
            "/trackings",
            "/projects",
            "/products",
            "/customer-service",
            "/finances",
            "/old-finances",
            "/reports/sales",
            "/reports/coupons",
            "/reports/pending",
            "/reports/blockedbalance",
            "/affiliates",
            "/apps",
            "/invitations",
            "/logout",
            "/send-authenticated",
            "/contestations",
        ];

        foreach ($excepts as $except) {
            if ("/" !== $except) {
                $except = trim($except, "/");
            }

            if (
                $request->fullUrlIs($except) ||
                $request->is($except) ||
                in_array(substr($request->fullUrl(), -4), ["xlsx", ".csv"])
            ) {
                return true;
            }
        }

        return false;
    }
}
