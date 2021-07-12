<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Whatsapp2Integration;
use Modules\Core\Services\Whatsapp2Service;

class GenericCommand extends Command
{
    protected $signature = 'generic';

    protected $description = 'Command description';

    // private $cloudflareService;

    // public function __construct()
    // {
    //     //parent::__construct();

    //     //$this->cloudflareService = new CloudFlareService();
    // }

    public function handle()
    {
        $sale = Sale::find(1101848);
        $whatsapp2Integration = Whatsapp2Integration::where('project_id', 2546)
            ->where('pix_expired', 1)
            ->first();
        if (!empty($whatsapp2Integration)) {
            $whatsapp2Service = new Whatsapp2Service(
                $whatsapp2Integration->url_checkout,
                $whatsapp2Integration->url_order,
                $whatsapp2Integration->api_token,
                $whatsapp2Integration->id
            );

            $whatsapp2Service->sendPixSaleExpired($sale);
        }
    }
}


