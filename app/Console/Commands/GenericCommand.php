<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\GetnetPaymentService;
use Modules\Core\Services\GetnetService;
use Modules\Core\Services\ShopifyService;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'generic {user?}';
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function handle()
    {
        $getnetService = new GetnetPaymentService();


        $paymentId = "57b3ba95-5216-4bb6-bb9d-02e78c0d326a";

        $data = [
            'releasePaymentDate' => "2020-07-05T00:00:00",
            'subsellerId' => '700050655',
            'productId' => '1',
            'productAmount' => '319'
        ];

        $getnetService->paymentRelease($paymentId, $data);

        //


        /*$getnetService = new GetnetService();
           $getnetService->checkPjCompanyRegister(28337339000105);
        $getnetService->getStatement();*/
    }
}


