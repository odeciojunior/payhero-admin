<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Gateway;
use Modules\Core\Services\Gateways\CheckoutGateway;
use Vinkla\Hashids\Facades\Hashids;

class ProcessPostbackPaylab extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "paylab:proccess-postback";

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
     * @return int
     */
    public function handle()
    {
        try {
            $rows = DB::table("ethoca_postbacks")
                ->select("id")
                ->where("processed_flag", false)
                ->orderBy("id", "ASC")
                ->limit(10)
                ->get();
            $this->line(count($rows) . " para processar");
            if (count($rows) > 0) {
                $checkout = new CheckoutGateway(Gateway::IUGU_PRODUCTION_ID);
                foreach ($rows as $row) {
                    $this->line($row->id);
                    $response = $checkout->processPostbackPaylab(Hashids::encode($row->id));
                    $this->line(json_encode($response));
                }
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
