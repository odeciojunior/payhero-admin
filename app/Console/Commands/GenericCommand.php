<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Core\Entities\Sale;
use Modules\Core\Events\PixExpiredEvent;
use Modules\Core\Services\CloudFlareService;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    private $cloudflareService;

    public function __construct()
    {
        parent::__construct();

        $this->cloudflareService = new CloudFlareService();
    }

    public function handle()
    {
        $lastPixSale = Sale::where('payment_method', Sale::PIX_PAYMENT)->get()->last();

        $lastPixSale->update([
            'created_at' => Carbon::now()->subHours(2)->toDateTimeString(),
            'status' => Sale::STATUS_PENDING
                             ]);

        dd(event(new PixExpiredEvent(Sale::latest()->first())));
    }
}



