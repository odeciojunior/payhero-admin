<?php

namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\User;
use Modules\Core\Events\SendEmailPendingDocumentEvent;
use Modules\Core\Services\EmailService;
use Modules\Core\Services\SendgridService;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class SendEmailPedingDocumentoListener
 * @package Modules\Core\Listeners
 */
class SendEmailPedingDocumentoListener implements ShouldQueue
{
    use Queueable;
    /**
     * @var SendgridService
     */
    private $emailService;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        $this->emailService = new EmailService();
    }

    /**
     * @param SendEmailPendingDocumentEvent $event
     */
    public function handle(SendEmailPendingDocumentEvent $event)
    {
        $data = $event->request;

        $sufixHomolog = "-test";
        if (env("APP_ENV") == "production") {
            $sufixHomolog = "";
        }

        $url = "https://accounts{$sufixHomolog}.azcend.com.br/personal-info";

        if (!empty($data["companyId"])) {
            $url =
                "https://accounts{$sufixHomolog}.azcend.com.br/companies/company-detail/" .
                Hashids::encode($data["companyId"]);
        }

        $company = null;
        if (!empty($data["companyId"])) {
            $company = Company::find($data["companyId"]);
        }

        $user = null;
        if (!empty($data["userId"])) {
            $user = User::find($data["userId"]);
        }

        if (empty($company) && empty($user)) {
            return;
        }

        try {
            $emailReturn = $this->emailService->sendEmail(
                "noreply@azcend.com.br",
                $data["domainName"],
                $data["clientEmail"],
                $data["clientName"],
                "d-c8c19c79591448cb9fb755631cab70d0", /// done
                [
                    "name" => $data["clientName"],
                    "account_url" => $url,
                ]
            );

            if ($emailReturn) {
                if (!empty($company)) {
                    $company->update([
                        "date_last_document_notification" => now(),
                    ]);
                }

                if (!empty($user)) {
                    $user->update([
                        "date_last_document_notification" => now(),
                    ]);
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }

    public function tags()
    {
        return ["listener:" . static::class, "SendEmailPedingDocumentoListener"];
    }
}
