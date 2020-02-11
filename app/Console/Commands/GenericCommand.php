<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\ProjectNotificationService;

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
    protected $signature = 'generic';
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
     * @throws PresenterException
     */
    public function handle()
    {
        $withdrawals = new Withdrawal();
        foreach ($withdrawals->cursor() as $withdrawal) {
            $withdrawal->update([
                                    'release_date_new' => $withdrawal->release_date,
                                ]);
        }
        dd('acabou');
        /* print_r('Terminou Frete');
         print_r('Começou checkout');

         $checkoutModel = new Checkout();

         foreach (Checkout::whereNull('status_enum')->orderBy('id', 'desc')->cursor() as $checkout) {

             $checkout->update([
                 'status_enum' => $checkoutModel->present()->getStatusEnum($checkout->status)
             ]);
         }
         print_r('Acabou checkout');*/
    }
}
