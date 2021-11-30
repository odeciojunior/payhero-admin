<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;

class CheckSalePixApproved extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:sale-pix-approved';

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

        $sales = Sale::where(
            [
                ['payment_method', '=', Sale::PIX_PAYMENT],
                ['status', '=', Sale::STATUS_CANCELED]
            ]
        )
        ->whereHas(
            'pixCharges',
            function ($querySale) {
                $querySale->where('status', 'RECEBIDO');
            }
        )->get();

        $total = count($sales);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

            foreach ($sales as $sale) {
                $this->line('  id: ' . $sale->id . '  ');
                $sale->update(
                    [
                        'status' => Sale::STATUS_APPROVED,
                        'gateway_status' => 'RECEBIDO',
                        'end_date' => Date('Y-m-d H:i:s')
                    ]
                );

                foreach (Transaction::where('sale_id', $sale->id)->get() as $transaction) {
                    $transaction->update(['status_enum' => 2,'status' => 'paid']);
                }
                $bar->advance();
            }

        $bar->finish();
    }
}
