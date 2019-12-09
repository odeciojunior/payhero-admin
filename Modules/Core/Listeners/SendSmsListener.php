<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Events\SendSmsEvent;
use Modules\Core\Services\SmsService;

class SendSmsListener implements ShouldQueue
{
    use Queueable;
    /**
     * @var SmsService
     */
    private $smsService;
    private $tag;

    /**
     * SendSmsListener constructor.
     */
    public function __construct()
    {
        $this->smsService = new SmsService();
    }

    /**
     * Handle the event.
     * @param SendSmsEvent $event
     * @return void
     */
    public function handle(SendSmsEvent $event)
    {
        $data      = $event->request;
        $this->tag = !empty($data['tag']) ? $data['tag'] : 'sendSmsListener';
        $sendSms   = $this->smsService->sendSms($data['telephone'], $data['message']);
        if ($sendSms) {
            $data['checkout']->increment('sms_sent_amount');
        }
    }

    public function tags()
    {
        return [!empty($this->tag) ? $this->tag : 'sendSmsListener'];
    }
}
