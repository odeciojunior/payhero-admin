<?php

namespace Modules\Core\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Entities\Checkout;
use Modules\Core\Events\SendEmailEvent;
use Modules\Core\Events\SendSmsEvent;
use Modules\Core\Services\SmsService;

class SendSmsListener implements ShouldQueue
{
    use Queueable;
    /**
     * @var SmsService
     */
    private $smsService;

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
        $data = $event->request;

        $sendSms = $this->smsService->sendSms($data["telephone"], $data["message"]);
        if ($sendSms) {
            Checkout::where("id", $data["checkout_id"])->increment("sms_sent_amount");
        }
    }

    public function tags()
    {
        return ["listener:" . static::class, "sendEmailListener"];
    }
}
