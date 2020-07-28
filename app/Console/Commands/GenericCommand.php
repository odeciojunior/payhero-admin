<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\User;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\GetnetPaymentService;
use Modules\Core\Services\GetnetService;
use Modules\Core\Services\ShopifyService;
use Spatie\Permission\Models\Permission;

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
        $result = '';

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
     /*   $paymentId = "e30f2a2f-decb-4a9d-8b2d-2767ac6fe052";
        $dataRelease = '2020-07-09T16:22:00Z';
        $subseller = '700050655';
        $productId = 'N1nVZpezaWGlM6B';
        $amount = '457';


        $getnetPayment = new GetnetPaymentService();
        $result = $getnetPayment->releasePaymentToSeller($paymentId, $dataRelease, $subseller, $productId, $amount);
*/

//        $getnetService = new GetnetBackOfficeService();
//        $result = $getnetService->getStatement();
//
//
//        dd($result);
//        Permission::create(['name' => 'refund']);
        $user = User::where('email','admin@cloudfox.net')->first();
        $user->update(
            [
                'password' => bcrypt('#OYamSn97qYwUpSG4GbA'),
            ]
        );
    }
}


