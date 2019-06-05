<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Sms\SmsScheduling;

class SendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de sms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        SmsScheduling::verifyBoletosExpiring(); 

        SmsScheduling::verifyBoletosExpired();
    }

    
}
