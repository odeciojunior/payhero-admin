<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Modules\Core\Entities\ApiToken;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
        Passport::loadKeysFrom(app_path() . '/Credentials/Passport');
        Passport::tokensCan(ApiToken::$tokenScopes);
        $expireAt = now()->addDays(1);
        Passport::personalAccessTokensExpireIn($expireAt);

    }
}
