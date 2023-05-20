<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\UpdateCompanyGetnetEvent;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class UpdateCompanyGetnetSendEmailListener
 * @package Modules\Core\Listeners
 */
class UpdateCompanyGetnetSendEmailListener implements ShouldQueue
{
    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param UpdateCompanyGetnetEvent $event
     */
    public function handle(UpdateCompanyGetnetEvent $event)
    {
        try {
            $emailService = new SendgridService();

            $company = $event->company;
            $user = $event->company->user;
            $data = [
                "first_name" => explode(" ", $user->name)[0],
                "company_name" => $company->fantasy_name,
                "companies_url" => "https://admin.nexuspay.vip/companies",
            ];

            $emailService->sendEmail(
                "noreply@nexuspay.com.br",
                "NexusPay",
                $user->email,
                $user->name,
                "d-8d2e3cdfae534616be1f885510d29b0a", // done
                $data
            );
        } catch (Exception $e) {
            report($e);
        }
    }
}
