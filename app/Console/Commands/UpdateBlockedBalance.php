<?php

namespace App\Console\Commands;

use Exception;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Tracking;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\BlockReason;
use Illuminate\Console\Command;

class UpdateBlockedBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-blocked-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public $blockReasons;

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

            $transactionModel = new Transaction();
            $salesModel = new Sale();

            $transactions = $transactionModel->with(['sale.tracking','sale.productsPlansSale'])
                ->join('sales', 'sales.id', 'transactions.sale_id')
                ->whereNull('invitation_id')
                ->where(function($queryStatus) use($transactionModel, $salesModel) {
                    $queryStatus->where(function($transfered) use($transactionModel) {
                        $transfered->where('transactions.status_enum', $transactionModel->present()->getStatusEnum('transfered'));
                    })
                        ->orWhere(function($pending) use($transactionModel, $salesModel) {
                            $pending->where('transactions.status_enum', $transactionModel->present()->getStatusEnum('paid'))
                                ->where('sales.status', $salesModel->present()->getStatus('in_dispute'));
                        });
                })
                ->whereDate('transactions.created_at', '>=', '2020-01-01')
                ->whereHas('sale',function ($f1) use ($salesModel) {
                    $f1->where('sales.status', $salesModel->present()->getStatus('in_dispute'))
                        ->orWhere('sales.has_valid_tracking', 0)
                        ->whereNotNull('delivery_id');
                }
                )
                ->whereIn('sales.status', [1,24]);

            $blockReasonModel = new BlockReason;
            $this->blockReasons = $blockReasonModel->get();

            foreach ($transactions->cursor() as $transaction) {

                $sale = $transaction->sale;
                $created = 0;

                if($sale->status == 24) {
                    $created = $this->createBlockReasonSale($sale->id, $blockReasonModel->present()->getReasonEnum('in_dispute'));
                }
                if($sale->tracking->count() < $sale->productsPlansSale->count()){
                    $created = $this->createBlockReasonSale($sale->id, $blockReasonModel->present()->getReasonEnum('without_tracking'));
                }
                if($sale->tracking->where('system_status_enum', (new Tracking())->present()->getSystemStatusEnum('duplicated'))->count()) {
                    $created = $this->createBlockReasonSale($sale->id, $blockReasonModel->present()->getReasonEnum('duplicated'));
                }
                if($sale->tracking->where('system_status_enum', (new Tracking())->present()->getSystemStatusEnum('no_tracking_info'))->count()) {
                    $created = $this->createBlockReasonSale($sale->id, $blockReasonModel->present()->getReasonEnum('no_tracking_info'));
                }
                if($sale->tracking->where('system_status_enum', (new Tracking())->present()->getSystemStatusEnum('unknown_carrier'))->count()) {
                    $created = $this->createBlockReasonSale($sale->id, $blockReasonModel->present()->getReasonEnum('unknown_carrier'));
                }
                if($sale->tracking->where('system_status_enum', (new Tracking())->present()->getSystemStatusEnum('posted_before_sale'))->count()) {
                    $created = $this->createBlockReasonSale($sale->id, $blockReasonModel->present()->getReasonEnum('posted_before_sale'));
                }
                if(empty($created)) {
                    $this->createBlockReasonSale($sale->id, $blockReasonModel->present()->getReasonEnum('others'));
                }
            }

        } catch (Exception $e) {
            report($e);
        }

    }

    public function createBlockReasonSale($saleId, $reason)
    {
        $blockReason = $this->blockReasons->where('reason_enum', $reason)->first();
        $this->info($saleId . ' -- ' . $reason);

        return BlockReasonSale::firstOrCreate([
            'sale_id' => $saleId,
            'blocked_reason_id' => $blockReason->id ?? null
        ], ['observation' => $blockReason->reason ?? 'motivo não listado']);
    }
}
