<?php

namespace Modules\Finances\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

/**
 * Class FinancesServiceProvider
 * @package Modules\Finances\Providers
 */
class FinancesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

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
        $this->loadMigrationsFrom(__DIR__ . "/../Database/Migrations");
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
        $this->publishes(
            [
                __DIR__ . "/../Config/config.php" => config_path("finances.php"),
            ],
            "config"
        );
        $this->mergeConfigFrom(__DIR__ . "/../Config/config.php", "finances");
    }

    /**
     * Register views.
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path("views/modules/finances");

        $sourcePath = __DIR__ . "/../Resources/views";

        $this->publishes(
            [
                $sourcePath => $viewPath,
            ],
            "views"
        );

        $this->loadViewsFrom(
            array_merge(
                array_map(function ($path) {
                    return $path . "/modules/finances";
                }, Config::get("view.paths")),
                [$sourcePath]
            ),
            "finances"
        );
    }

    /**
     * Register translations.
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path("lang/modules/finances");

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, "finances");
        } else {
            $this->loadTranslationsFrom(__DIR__ . "/../Resources/lang", "finances");
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (!app()->environment("production")) {
            app(Factory::class)->load(__DIR__ . "/../Database/factories");
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
