<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use Closure;

class PermissionMiddleware
{
    public function handle($request, Closure $next, $permission, $guard = null)
    {
        if (
            app("auth")
                ->guard($guard)
                ->guest()
        ) {
            throw UnauthorizedException::notLoggedIn();
        }

        $permissions = is_array($permission) ? $permission : explode("|", $permission);

        foreach ($permissions as $permission) {
            if (
                app("auth")
                    ->guard($guard)
                    ->user()
                    ->can($permission)
            ) {
                return $next($request);
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
