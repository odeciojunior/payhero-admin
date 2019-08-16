<?php

namespace Modules\Withdrawals\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class WithdrawalsServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
                             __DIR__ . '/../Config/config.php' => config_path('withdrawals.php'),
                         ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'withdrawals'
        );
    }

    /**
     * Register views.
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/withdrawals');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
                             $sourcePath => $viewPath,
                         ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function($path) {
            return $path . '/modules/withdrawals';
        }, \Config::get('view.paths')), [$sourcePath]), 'withdrawals');
    }

    /**
     * Register translations.
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/withdrawals');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'withdrawals');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'withdrawals');
        }
    }

    /**
     * Register an additional directory of factories.
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
