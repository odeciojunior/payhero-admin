<?php

namespace Modules\ProjectReviewsConfig\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ProjectReviewsConfigServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
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
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes(
            [
                __DIR__ . "/../Config/config.php" => config_path("projectreviewsconfig.php"),
            ],
            "config"
        );
        $this->mergeConfigFrom(__DIR__ . "/../Config/config.php", "projectreviewsconfig");
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path("views/modules/projectreviewsconfig");

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
                    return $path . "/modules/projectreviewsconfig";
                }, \Config::get("view.paths")),
                [$sourcePath]
            ),
            "projectreviewsconfig"
        );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path("lang/modules/projectreviewsconfig");

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, "projectreviewsconfig");
        } else {
            $this->loadTranslationsFrom(__DIR__ . "/../Resources/lang", "projectreviewsconfig");
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment("production") && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . "/../Database/factories");
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
