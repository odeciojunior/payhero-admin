<?php

namespace App\Console\Commands;

use App\Jobs\SendNotazzInvoiceJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\NotazzService;

class GenericCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "generic {name?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

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
        $gateways = DB::table("gateways")->get();
        foreach ($gateways as $gateway) {
            $jsonConfig = FoxUtils::xorEncrypt($gateway->json_config, "decrypt");
            \Log::info(json_decode($jsonConfig ?? ""));
            break;
        }
    }
}
