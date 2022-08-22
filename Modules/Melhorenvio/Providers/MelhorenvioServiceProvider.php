<?php

namespace Modules\Melhorenvio\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Core\Services\MelhorenvioService;

class MelhorenvioServiceProvider extends ServiceProvider
{
    protected $moduleName = "Melhorenvio";

    protected $moduleNameLower = "melhorenvio";

    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind("melhorenvio", function () {
            return new MelhorenvioService();
        });
    }

    protected function registerConfig()
    {
        $this->publishes(
            [
                module_path($this->moduleName, "Config/config.php") => config_path($this->moduleNameLower . ".php"),
            ],
            "config"
        );
        $this->mergeConfigFrom(module_path($this->moduleName, "Config/config.php"), $this->moduleNameLower);
    }

    public function registerViews()
    {
        $viewPath = resource_path("views/modules/" . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, "Resources/views");

        $this->publishes(
            [
                $sourcePath => $viewPath,
            ],
            ["views", $this->moduleNameLower . "-module-views"]
        );

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get("view.paths") as $path) {
            if (is_dir($path . "/modules/" . $this->moduleNameLower)) {
                $paths[] = $path . "/modules/" . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
