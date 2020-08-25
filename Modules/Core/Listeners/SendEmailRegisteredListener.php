<?php


namespace Modules\Core\Listeners;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\UserRegisteredEvent;
use Modules\Core\Services\EmailService;

/**
 * Class SendEmailRegisteredListener
 * @package Modules\Core\Listeners
 */
class SendEmailRegisteredListener implements ShouldQueue
{
    use Queueable;

    /**
     * @var EmailService
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
     * @param UserRegisteredEvent $data
     */
    public function handle(UserRegisteredEvent $event) {
        $data      = $event->request;
        try {
            $this->emailService->sendEmail(
                'noreply@' . $data['domainName'],
                $data['domainName'],
                $data['clientEmail'],
                $data['clientName'],
                $data['templateId'],
                $data['bodyEmail']
            );
        } catch (Exception $e) {
            report($e);
        }
    }
}
