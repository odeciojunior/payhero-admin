<?php

namespace App\Console\Commands\System;

use Illuminate\Console\Command;
use Modules\Core\Services\EmailService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\SystemStatusService;

class CheckSystems extends Command
{
    protected $signature = 'check:systems';

    protected $description = 'Command description';

    private array $phoneList;

    public function __construct()
    {
        parent::__construct();
        $this->phoneList = [
            '5555996931098',
            '5522981071202',
            '5524998345779',
            '5575981031983',
            '5553999364177'
        ];
    }

    public function handle()
    {
        try {
            $smsService = new SmsService();

            $systemsStatus = (new SystemStatusService())->checkSystems();

            $status = collect(array_values($systemsStatus));

            if ($status->contains('status', '=', 'warning')) {
                (new EmailService())->sendEmailUnderAttack($systemsStatus);

                foreach ($this->phoneList as $phone) {
                    $smsService->sendSms(
                        $phone,
                        'Temos um possível problema com uma das plataformas, verifique o dashboard do Manager'
                    );
                }
            }
        } catch (\Exception $e) {
            report($e);
        }
    }
}
