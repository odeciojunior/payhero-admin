<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\SendEmailEvent;
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
    private $sendGridService;
    private $tag;

    /**
     * Create the event listener.
     * @return void
     */
    public function __construct()
    {
        $this->sendGridService = new SendgridService();
    }

    /**
     * @param SendEmailEvent $event
     */
    public function handle(SendEmailEvent $event)
    {
        $data      = $event->request;
        $this->tag = !empty($data['tag']) ? $data['tag'] : 'sendEmailListener';

        $smsReturn = $this->sendGridService->sendEmail('noreply@' . $data['domainName'], $data['projectName'], $data['clientEmail'], $data['clientName'], $data['templateId'], $data['bodyEmail']);
        if ($smsReturn) {
            $data['checkout']->increment('email_sent_amount');
        }
    }

    public function tags()
    {
        return ['listener:' . static::class, !empty($this->tag) ? $this->tag : 'sendEmailListener'];
    }
}
