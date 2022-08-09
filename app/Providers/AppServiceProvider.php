<?php

namespace App\Providers;

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
use Modules\Core\Observers\SaleObserver;
use Modules\Core\Observers\GatewaysCompaniesCredentialObserver;
use Modules\Core\Observers\TicketMessageObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //set timezone para este
        date_default_timezone_set("America/Sao_Paulo");

        Queue::failing(function (JobFailed $event) {
            report($event->exception);
        });

        Sale::observe(SaleObserver::class);
        Company::observe(CompanyObserver::class);
        GatewaysCompaniesCredential::observe(GatewaysCompaniesCredentialObserver::class);
        TicketMessage::observe(TicketMessageObserver::class);

        Paginator::useBootstrap();

        //força uso do https
        if (env("APP_ENV", "local") !== "local") {
            URL::forceScheme("https");
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
