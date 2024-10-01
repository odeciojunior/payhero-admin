<?php

namespace App\Console\Commands\Demo;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Checkout;

class AbandonedCartCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:abandoned-cart-checkout';

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
     * @return int
     */
    public function handle()
    {
        Config::set('database.default', 'demo');

        $checkouts = DB::table('checkouts as c')->select('c.id')->where('c.status_enum', Checkout::STATUS_ACCESSED)
        ->leftJoin('sales as s', 'c.id', '=', 's.checkout_id')
        ->whereNull('s.id')->where('c.created_at', '<=', Carbon::now()->subDay())
        ->get();

        foreach ($checkouts as $checkout) {
            Checkout::find($checkout->id)->update([
                'status' => 'abandoned cart',
                'status_enum' => Checkout::STATUS_ABANDONED_CART
            ]);

            $this->line('Atualizando checkout '.$checkout->id);
        }
    }
}
