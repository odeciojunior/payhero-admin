<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\NuvemshopIntegration;
use Modules\Core\Services\Nuvemshop\NuvemshopAPI;

class GenericCommand extends Command
{
    protected $signature = "generic";
    protected $description = "Get encrypted config";
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $nuvemshopIntegration = NuvemshopIntegration::first();

        $service = new NuvemshopAPI($nuvemshopIntegration->store_id, $nuvemshopIntegration->token);

        dd($service->findAllProducts(["page" => 1, "per_page" => 1]));
    }
}
