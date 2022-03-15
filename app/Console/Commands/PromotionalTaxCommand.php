<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\PromotionalTax;
use Modules\Core\Services\UserService;

class PromotionalTaxCommand extends Command
{
    protected $signature = 'verify:promotional-tax';

    protected $description = 'check if the promotional rate has expired';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {

            $this->addExpirationDatePromotionalTax();
            $this->removePromotionalTax();

        } catch (Exception $e) {
            report($e);
        }

    }

    private function removePromotionalTax(): void
    {
        $today = Carbon::now();
        $promotional_taxes = PromotionalTax::whereNotNull('expiration')
            ->where('active', true)
            ->where('expiration', '<', $today->format('Y-m-d'))
            ->get();

        foreach ($promotional_taxes as $promotional_tax) {
            (new UserService())->removePromotionalTax($promotional_tax);
        }
    }

    private function addExpirationDatePromotionalTax(): void
    {
        $promotional_taxes_without_expires = PromotionalTax::whereNull('expiration')->where('active', true)->get();
        foreach ($promotional_taxes_without_expires as $promotional_taxes_without_expire) {
            (new UserService())->addExpirationDatePromotionalTax($promotional_taxes_without_expire);
        }
    }
}
