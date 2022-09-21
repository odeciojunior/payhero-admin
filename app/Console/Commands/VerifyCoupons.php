<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\DiscountCoupon;

class VerifyCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "verify:coupons";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Desabilita cupons vencidos";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        DiscountCoupon::where("status", 1)
            ->where("expires", "<=", now())
            ->whereNotNull("expires")
            ->update([
                "status" => 0,
            ]);
    }
}
