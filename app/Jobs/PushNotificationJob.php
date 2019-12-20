<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Core\Entities\PushNotification;
use Modules\Mobile\Http\Controllers\Apis\v10\NotificationMachine;

class PushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var PushNotification
     */
    private $pushNotification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pushNotification)
    {
        $this->pushNotification = $pushNotification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            $notificationMachine = new NotificationMachine();
            $notificationMachine->init($this->pushNotification);

        } catch (Exception $e) {
            Log::warning('PushNotification - Erro no job ');
            report($e);
        }
    }
}
