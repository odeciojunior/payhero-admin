<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Sale;
use Modules\Core\Services\GetnetBackOfficeService;
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
        /*  $getnetService = new GetnetPaymentService();

          $paymentId = "db5983c6-2bc4-4504-82ac-11b20d89c1e0";

          $data = [
              'releasePaymentDate' => "2020-07-08T00:00:00Z",
              'subsellerId' => '700050664',
              'productId' => "2wq7GrYq0MZBANP",
              'productAmount' => 406
          ];

          $getnetService->paymentRelease($paymentId, $data);*/

        //


        $getnetService = new GetnetBackOfficeService();
        $result = $getnetService->getStatement(1);

        dd($result);
    }
}


