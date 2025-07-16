<?php

namespace App\Providers;

use App\Services\WhitelabelService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WhitelabelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register the configuration
        $this->mergeConfigFrom(
            base_path('config/whitelabel.php'),
            'whitelabel'
        );

        // Register the service as singleton
        $this->app->singleton('whitelabel', function ($app) {
            return new WhitelabelService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register middleware
        $this->app['router']->aliasMiddleware('whitelabel', \App\Http\Middleware\WhitelabelMiddleware::class);

        // Register Blade directives
        $this->registerBladeDirectives();

        // Register view composers
        $this->registerViewComposers();

        // Publish config
        $this->publishes([
            base_path('config/whitelabel.php') => config_path('whitelabel.php'),
        ], 'whitelabel-config');

        // Publish assets
        $this->publishes([
            public_path('images/clients') => public_path('images/clients'),
        ], 'whitelabel-assets');
    }

    /**
     * Register Blade directives
     */
    private function registerBladeDirectives()
    {
        // @whitelabel('key')
        Blade::directive('whitelabel', function ($expression) {
            return "<?php echo app('whitelabel')->get($expression); ?>";
        });

        // @whitelabelColor('key')
        Blade::directive('whitelabelColor', function ($expression) {
            return "<?php echo app('whitelabel')->getColor($expression); ?>";
        });

        // @whitelabelLogo('type')
        Blade::directive('whitelabelLogo', function ($expression) {
            return "<?php echo app('whitelabel')->getLogo($expression); ?>";
        });

        // @whitelabelFont('type')
        Blade::directive('whitelabelFont', function ($expression) {
            return "<?php echo json_encode(app('whitelabel')->getFont($expression)); ?>";
        });

        // @whitelabelClient
        Blade::directive('whitelabelClient', function () {
            return "<?php echo app('whitelabel')->getCurrentClient(); ?>";
        });

        // @whitelabelStyles
        Blade::directive('whitelabelStyles', function () {
            return "<?php echo app('whitelabel')->generateStyles(); ?>";
        });
    }

    /**
     * Register view composers
     */
    private function registerViewComposers()
    {
        view()->composer('*', function ($view) {
            $view->with('whitelabel', app('whitelabel'));
        });
    }
}