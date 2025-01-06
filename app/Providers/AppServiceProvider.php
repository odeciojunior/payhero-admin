<?php

declare(strict_types=1);

namespace App\Providers;

use GuzzleHttp\Client;
use Illuminate\Pagination\Paginator;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\TicketMessage;
use Modules\Core\Observers\CompanyObserver;
use Modules\Core\Observers\GatewaysCompaniesCredentialObserver;
use Modules\Core\Observers\SaleObserver;
use Modules\Core\Observers\TicketMessageObserver;
use Modules\GatewayIntegrations\Clients\ShortIOClient;
use Modules\GatewayIntegrations\Gateways\ShortenLinks\contract\ShortenLinkGatewayInterface;
use Modules\GatewayIntegrations\Gateways\ShortenLinks\ShortIOGateway;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //set timezone para este
        date_default_timezone_set("America/Sao_Paulo");

        Queue::failing(function (JobFailed $event): void {
            report($event->exception);
        });

        Sale::observe(SaleObserver::class);
        Company::observe(CompanyObserver::class);
        GatewaysCompaniesCredential::observe(GatewaysCompaniesCredentialObserver::class);
        TicketMessage::observe(TicketMessageObserver::class);

        Paginator::useBootstrap();

        //força uso do https
        if ("local" !== env("APP_ENV", "local")) {
            URL::forceScheme("https");
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            ShortenLinkGatewayInterface::class,
            function () {
                return new ShortIOGateway(
                    new ShortIOClient(
                        new Client(
                            [
                                'base_uri' => config('services.short_io.domain'),
                                'headers' => [
                                    'Accept' => 'application/json',
                                    'Content-Type' => 'application/json',
                                    'Authorization' => config('services.short_io.api_key'),
                                ],
                            ]
                        )
                    )
                );
            }
        );
    }
}
