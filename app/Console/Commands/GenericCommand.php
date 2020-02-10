<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Checkout;
use Modules\Core\Entities\Project;
use Modules\Core\Entities\Shipping;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\ProjectNotificationService;

/**
 * Class GenericCommand
 * @package App\Console\Commands
 */
class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generic';

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
     * @throws PresenterException
     */
    public function handle()
    {
        $shippingsModel = new Shipping();
        foreach (Shipping::cursor() as $shipping) {
            if ($shipping->type == 'pac' || $shipping->type == 'sedex' || $shipping->type == 'static') {
                $shipping->update([
                    'type_enum' => $shippingsModel->present()->getTypeEnum($shipping->type)
                ]);

            } else {
                if ($shipping->type == 'sexed') {
                    $shipping->update([
                        'type' => 'sedex',
                        'type_enum' => $shippingsModel->present()->getTypeEnum('sedex')
                    ]);
                }else{
                    printf('vazio');

                }


            }
        }
        dd('acabou');


    }
}
