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
        //-------------------------------
        //        Passport::routes();
        //        Passport::tokensExpireIn(now()->addMinutes('30'));
        //        Passport::loadKeysFrom(app_path() . '/Credentials/Passport');
        Passport::tokensCan(ApiToken::$tokenScopes);
        //tokens duram 5 anos
        /** @var Carbon $expireAt */
        $expireAt = now()->addYears(5);
        Passport::personalAccessTokensExpireIn($expireAt);
        //-------------------------------
    }
}
