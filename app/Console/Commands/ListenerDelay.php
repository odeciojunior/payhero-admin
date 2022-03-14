<?php

namespace App\Console\Commands;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Services\SmsService;

class ListenerDelay extends Command
{

    protected $signature = 'listener:delay';

    protected $description = 'Test Listener Delay';

    public function handle()
    {
        event(new ListenerDelayEvent("Rodou às " . now()->format('H:i:s')));
    }
}

class ListenerDelayEvent
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

class ListenerDelayListener implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        $this->delay(60);
    }

    public function handle(ListenerDelayEvent $event)
    {
        $smsService = new SmsService();
        $smsService->sendSms('5524998345779', $event->message . ' Enviou às : ' .  now()->format('H:i:s'));
    }
}
