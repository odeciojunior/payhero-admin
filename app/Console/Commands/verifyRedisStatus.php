<?php

namespace App\Console\Commands;

use SendGrid;
use Exception;
use Predis\Client;
use Illuminate\Console\Command;
use Modules\Core\Services\SmsService;

class verifyRedisStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try{
            $redisClient = new Client();

            $redisClient->get('test-connection');
        }
        catch(Exception $e){
            // redis OFF

            // email addresses for notify
            $emails = [
                'julioleichtweis@gmail.com',
            ];

            // phone numbers for notify
            $phoneNumbers = [
                '5555996931098',
            ];

            $sendgrid =  new SendGrid(getenv('SENDGRID_API_KEY'));
            $smsService = new SmsService();

            foreach($emails as $email){

                try{
                    $sendgridMail = new \SendGrid\Mail\Mail();
                    $sendgridMail->setFrom('noreply@cloudfox.net', 'cloudfox');
                    $sendgridMail->addTo($email, 'cloudfox');
                    $sendgridMail->addDynamicTemplateDatas([
                        'server' => 'ADMIN'
                    ]);
                    $sendgridMail->setTemplateId('d-413a13e7bfbe412a9531037402872cff');

                    $response   = $sendgrid->send($sendgridMail);
                }
                catch(Exception $e){
                    //
                }

            }

            foreach($phoneNumbers as $phoneNumber){

                try{
                    $smsService->sendSms($phoneNumber, 'Admin - redis caiu');
                }
                catch(Exception $e){
                    //
                }
            }

        }
    }
}
