<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Checkout;

class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generic';

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

        $checkoutModel = new Checkout();

        foreach (Checkout::whereNull('status_enum')->orderBy('id', 'desc')->cursor() as $checkout) {

            $checkout->update([
                'status_enum' => $checkoutModel->present()->getStatusEnum($checkout->status)
            ]);
        }

        dd("Feito");
    }
}
