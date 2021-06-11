<?php

namespace App\Console\Commands;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Modules\Core\Services\CloudFlareService;
use Modules\Plans\Http\Controllers\PlansApiController;

class GenericCommand extends Command
{
    protected $signature = 'generic {user?}';

    protected $description = 'Command description';

    // private $cloudflareService;

    // public function __construct()
    // {
    //     //parent::__construct();

    //     //$this->cloudflareService = new CloudFlareService();
    // }

    public function handle()
    {
    }
}



