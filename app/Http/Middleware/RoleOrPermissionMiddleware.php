<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UnauthorizedException;

class RoleOrPermissionMiddleware
{
    public function handle($request, Closure $next, $roleOrPermission, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $rolesOrPermissions = is_array($roleOrPermission) ? $roleOrPermission : explode("|", $roleOrPermission);

        if (
            !Auth::guard($guard)
                ->user()
                ->hasAnyRole($rolesOrPermissions) &&
            !Auth::guard($guard)
                ->user()
                ->hasAnyPermission($rolesOrPermissions)
        ) {
            throw UnauthorizedException::forRolesOrPermissions($rolesOrPermissions);
        }

        return $next($request);
    }
}
