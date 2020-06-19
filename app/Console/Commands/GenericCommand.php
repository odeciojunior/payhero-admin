<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\TrackingCodeUpdatedEvent;
use Modules\Core\Services\GetnetService;
use Modules\Core\Services\ProductService;
use Modules\Core\Services\ShopifyService;
use Modules\Core\Services\SmsService;
use Modules\Core\Services\TrackingService;

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

    public function handle()
    {
        $getNetService = new GetnetService();

        $getNetService->checkAvailablePaymentPlans();
        $getNetService->createPfCompany();
    }
}


