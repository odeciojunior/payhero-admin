<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Modules\Core\Entities\ApiToken;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [
        "App\Model" => "App\Policies\ModelPolicy",
    ];

    /**
     * Register any authentication / authorization services.
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        Passport::loadKeysFrom(app_path() . "/Credentials/Passport");
        Passport::tokensCan(ApiToken::$tokenScopes);
        $expireAt = now()->addDays(1);
        Passport::personalAccessTokensExpireIn($expireAt);

        // Gate::after(function ($user, $ability) {
        //     return $user->hasRole("admin") ? true : null;
        // });
    }
}
