<?php

namespace App\Console\Commands;

use App\Jobs\SendNotazzInvoiceJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\NotazzInvoice;
use Modules\Core\Entities\Transaction;
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
        try {
            $notazzInvoiceModel = new NotazzInvoice();

            $notazzInvoices = $notazzInvoiceModel
                ->with(["sale", "notazzIntegration"])
                ->whereIn("status", [
                    $notazzInvoiceModel->present()->getStatus("in_process")
                ])
                ->where("schedule", "<", Carbon::now())
                ->get();

            foreach ($notazzInvoices as $notazzInvoice) {
                $transaction = Transaction::where('sale_id', $notazzInvoice->sale_id)->where('type', Transaction::TYPE_PRODUCER)->where('tax_type', Transaction::TYPE_VALUE_TAX)->first();
                if(!empty($transaction)) {
                    //cria as jobs para enviar as invoices
                    $notazzInvoice->update([
                        "status" => $notazzInvoiceModel->present()->getStatus("in_process"),
                    ]);

                    SendNotazzInvoiceJob::dispatch($notazzInvoice->id)->delay(rand(1, 3));
                }
            }
        } catch(Exception $ex) {
            report($ex);
        }

        dd("Fim");


    }
}
