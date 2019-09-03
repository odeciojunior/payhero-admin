<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Services\PusherService;
use Modules\Notifications\Notifications\boletoCompensatedNotification;

class sendpusher extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'send:newpushernotification';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        /*$pusher = new PusherService();
        $use    = User::find(14);
        Notification::send($use, new boletoCompensatedNotification(14, 7, $pusher));*/

    }
}
