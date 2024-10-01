<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $roles = is_array($role) ? $role : explode("|", $role);

        if (
            !Auth::guard($guard)
                ->user()
                ->hasAnyRole($roles)
        ) {
            throw UnauthorizedException::forRoles($roles);
        }

        return $next($request);
    }
}
