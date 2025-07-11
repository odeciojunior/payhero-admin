<?php

namespace App\Http;

use App\Http\Middleware\AuthApiV1;
use App\Http\Middleware\Broadcast;
use App\Http\Middleware\CheckForMaintenanceMode;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\InternalApiAuth;
use App\Http\Middleware\IsCloudFoxAccount;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\ThrottleRequests;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\VerifyShopifyPostback;
use App\Http\Middleware\DemoAccount;
use App\Http\Middleware\CheckAccountStatusWeb;
use App\Http\Middleware\CheckAccountStatusApi;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;
use Laravel\Passport\Http\Middleware\CheckScopes;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\VerifyFrozenAccountWeb;
use App\Http\Middleware\VerifyFrozenAccountApi;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        TrustProxies::class,
        HandleCors::class,
    ];

    /**
     * The application's route middleware groups.
     * @var array
     */
    protected $middlewareGroups = [
        "web" => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            CheckAccountStatusWeb::class,
            VerifyFrozenAccountWeb::class,
            \App\Http\Middleware\WhitelabelMiddleware::class,
        ],

        "api" => ["throttle:500,1", "bindings", CheckAccountStatusApi::class, VerifyFrozenAccountApi::class],
    ];
    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     * @var array
     */
    protected $routeMiddleware = [
        "auth" => Authenticate::class,
        "auth.basic" => AuthenticateWithBasicAuth::class,
        "bindings" => SubstituteBindings::class,
        "cache.headers" => SetCacheHeaders::class,
        "can" => Authorize::class,
        "guest" => RedirectIfAuthenticated::class,
        "signed" => ValidateSignature::class,
        "throttle" => ThrottleRequests::class,
        "VerifyShopifyPostback" => VerifyShopifyPostback::class,
        "broadcast" => Broadcast::class,
        "role" => RoleMiddleware::class,
        "permission" => PermissionMiddleware::class,
        "role_or_permission" => RoleOrPermissionMiddleware::class,
        "scopes" => CheckScopes::class,
        "scope" => CheckForAnyScope::class,
        "InternalApiAuth" => InternalApiAuth::class,
        "IsCloudFoxAccount" => IsCloudFoxAccount::class,
        "demo_account" => DemoAccount::class,
        "authApiV1" => AuthApiV1::class,
    ];
}
