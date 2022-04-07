<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Checkout;
use Modules\Core\Events\SendEmailEvent;
use Modules\Core\Services\EmailService;
use Modules\Core\Services\SendgridService;

/**
 * Class SendEmailListener
 * @package Modules\Core\Listeners
 */
class SendEmailListener implements ShouldQueue
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
     * @param SendEmailEvent $event
     */
    public function handle(SendEmailEvent $event)
    {
        $data      = $event->request;
        $emailReturn = $this->emailService->sendEmail('help@' . $data['domainName'], $data['projectName'], $data['clientEmail'], $data['clientName'], $data['templateId'], $data['bodyEmail']);
        if ($emailReturn) {
            Checkout::where('id', $data['checkout_id'])->increment('email_sent_amount');
        }
    }

    public function tags()
    {
        return ['listener:' . static::class, 'SendEmailListener'];
    }
}
