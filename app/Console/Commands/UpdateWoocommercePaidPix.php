<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\WooCommerceIntegration;
use Modules\Core\Services\WooCommerceService;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Log;

class UpdateWoocommercePaidPix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:update-paid-pix';

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

        Log::debug('command . ' . __CLASS__ . ' . iniciando em ' . date("d-m-Y H:i:s"));

        try {

            $sales = Sale::whereNotNull('woocommerce_order')->where('payment_method', Sale::PIX_PAYMENT)->where('status', Sale::STATUS_APPROVED)->get();

            $output = new ConsoleOutput();
            $progress = new ProgressBar($output, count($sales));
            $progress->start();

            foreach($sales as $sale) {
                $projectId = $sale->project_id;

                $integration = WooCommerceIntegration::where('project_id', $projectId)->first();
                if(!empty($integration)) {
                    $service = new WooCommerceService($integration->url_store, $integration->token_user, $integration->token_pass);

                    $service->approvePix($sale->woocommerce_order);
                }

                $progress->advance();
            }

            $progress->finish();
            $output->writeln('');

        } catch (Exception $e) {
            report($e);
        }

        Log::debug('command . ' . __CLASS__ . ' . finalizando em ' . date("d-m-Y H:i:s"));

    }
}
