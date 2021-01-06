<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Getnet\Models\Detail;
use Modules\Getnet\Models\SaleSearch;
use Modules\Getnet\Models\Search;
use Modules\Getnet\Models\Summary;

class VerifyTransfersGetnetWithoutDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:transfers-getnet-without-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Faz uma varredura e salva em um banco paralelo o extrato da GETNET';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $transactionModel = new Transaction();

        $search = new Search();
        $search->save();

        $this->comment(now()->format('H:i:s'));
        $this->comment('............');

        $sales = Sale::select('sales.id')
            ->join('transactions', 'sales.id', '=', 'transactions.sale_id')
            ->whereIn('sales.gateway_id', [15])
            ->where('transactions.release_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('transactions.status_enum', $transactionModel->present()->getStatusEnum('paid'))
            ->where(
                function ($q) {
                    $q->where('sales.has_valid_tracking', true)->orWhereNull('sales.delivery_id');
                }
            )
            ->distinct();
        //->where('transactions.id', 2116172)
        //->get(); //3965
        //->count(); //4068
        //->whereIn('sales.id', [860451]);

        $this->comment('......' . $sales->count() . '......');

        foreach ($sales->cursor() as $sale) {

            try {

                $saleMonitor = \Modules\Getnet\Models\Sale::find($sale->id);

                if (!$saleMonitor) {

                    $saleMonitor = new \Modules\Getnet\Models\Sale();

                    $saleMonitor->id = $sale->id;
                    $saleMonitor->hash_id = $sale->hash_id;
                    $saleMonitor->save();

                    $saleMonitor = \Modules\Getnet\Models\Sale::find($sale->id);
                }

                $saleSearch = new SaleSearch();
                $saleSearch->search_id = $search->id;
                $saleSearch->sale_id = $sale->id;
                $saleSearch->save();

                $getNetBackOfficeService = new GetnetBackOfficeService();
                $getNetBackOfficeService->setStatementSaleHashId($saleMonitor->hash_id);

                $originalResult = $getNetBackOfficeService->getStatement();
                $result = json_decode($originalResult);

                if (isset($result->list_transactions)) {

                    $saleSearch->list_transactions_count = count($result->list_transactions);
                    $saleSearch->data = $originalResult;
                    $saleSearch->save();

                    foreach ($result->list_transactions as $statement) {

                        $summary = $statement->summary;
                        $details = $statement->details;

                        $summaryMonitor = new Summary();

                        $summaryMonitor->search_id = $search->id;
                        $summaryMonitor->sale_id = $sale->id;
                        $summaryMonitor->details_count = count($details);
                        $summaryMonitor->type_register = $summary->type_register;
                        $summaryMonitor->order_id = $summary->order_id;
                        $summaryMonitor->seller_id = $summary->seller_id;
                        $summaryMonitor->marketplace_subsellerid = $summary->marketplace_subsellerid;
                        $summaryMonitor->merchand_id = $summary->merchand_id;
                        $summaryMonitor->cnpj_marketplace = $summary->cnpj_marketplace;
                        $summaryMonitor->marketplace_transaction_id = $summary->marketplace_transaction_id;
                        $summaryMonitor->transaction_date = $summary->transaction_date;
                        $summaryMonitor->confirmation_date = $summary->confirmation_date;
                        $summaryMonitor->product_id = $summary->product_id;
                        $summaryMonitor->transaction_type = $summary->transaction_type;
                        $summaryMonitor->number_installments = $summary->number_installments;
                        $summaryMonitor->nsu_host = $summary->nsu_host;
                        $summaryMonitor->acquirer_transaction_id = $summary->acquirer_transaction_id;
                        $summaryMonitor->card_payment_amount = $summary->card_payment_amount;
                        $summaryMonitor->sum_details_card_payment_amount = $summary->sum_details_card_payment_amount;
                        $summaryMonitor->marketplace_original_transaction_id = $summary->marketplace_original_transaction_id;
                        $summaryMonitor->transaction_status_code = $summary->transaction_status_code;
                        $summaryMonitor->transaction_sign = $summary->transaction_sign;
                        $summaryMonitor->terminal_nsu = $summary->terminal_nsu;
                        $summaryMonitor->reason_message = $summary->reason_message;
                        $summaryMonitor->authorization_code = $summary->authorization_code;
                        $summaryMonitor->payment_id = $summary->payment_id;
                        $summaryMonitor->terminal_identification = $summary->terminal_identification;
                        $summaryMonitor->nsu_tef = $summary->nsu_tef;
                        $summaryMonitor->entry_mode = $summary->entry_mode;
                        $summaryMonitor->transaction_channel = $summary->transaction_channel;
                        $summaryMonitor->capture = $summary->capture;
                        $summaryMonitor->payment_tag = $summary->payment_tag;

                        $summaryMonitor->save();

                        if (empty($saleMonitor->order_id)) {

                            $saleMonitor->order_id = $summaryMonitor->order_id;
                            $saleMonitor->save();
                        }

                        foreach ($details as $detail) {

                            $detailMonitor = new Detail();
                            $detailMonitor->search_id = $search->id;
                            $detailMonitor->sale_id = $sale->id;
                            $detailMonitor->summary_id = $summaryMonitor->id;
                            $detailMonitor->type_register = $detail->type_register;
                            $detailMonitor->bank = $detail->bank;
                            $detailMonitor->agency = $detail->agency;
                            $detailMonitor->account_number = $detail->account_number;
                            $detailMonitor->account_type = $detail->account_type;
                            $detailMonitor->marketplace_schedule_id = $detail->marketplace_schedule_id;
                            $detailMonitor->marketplace_subsellerid = $detail->marketplace_subsellerid;
                            $detailMonitor->nu_liquid = $detail->nu_liquid;
                            $detailMonitor->release_status = $detail->release_status;
                            $detailMonitor->merchand_id = $detail->merchand_id;
                            $detailMonitor->cpfcnpj_subseller = $detail->cpfcnpj_subseller;
                            $detailMonitor->cancel_custom_key = $detail->cancel_custom_key;
                            $detailMonitor->cancel_request_id = $detail->cancel_request_id;
                            $detailMonitor->marketplace_transaction_id = $detail->marketplace_transaction_id;
                            $detailMonitor->cnpj_marketplace = $detail->cnpj_marketplace;
                            $detailMonitor->transaction_date = $detail->transaction_date;
                            $detailMonitor->confirmation_date = $detail->confirmation_date;
                            $detailMonitor->item_id = $detail->item_id;
                            $detailMonitor->number_installments = $detail->number_installments;
                            $detailMonitor->installment = $detail->installment;
                            $detailMonitor->installment_date = $detail->installment_date;
                            $detailMonitor->installment_amount = $detail->installment_amount;
                            $detailMonitor->subseller_rate_amount = $detail->subseller_rate_amount;
                            $detailMonitor->subseller_rate_percentage = $detail->subseller_rate_percentage;
                            $detailMonitor->payment_date = $detail->payment_date;
                            $detailMonitor->subseller_rate_closing_date = $detail->subseller_rate_closing_date;
                            $detailMonitor->subseller_rate_confirm_date = $detail->subseller_rate_confirm_date;
                            $detailMonitor->subseller_id = $detail->subseller_id;
                            $detailMonitor->seller_id = $detail->seller_id;
                            $detailMonitor->transaction_sign = $detail->transaction_sign;
                            $detailMonitor->item_id_mgm = $detail->item_id_mgm;
                            $detailMonitor->payment_id = $detail->payment_id;
                            $detailMonitor->payment_tag = $detail->payment_tag;
                            $detailMonitor->item_split_tag = $detail->item_split_tag;
                            $detailMonitor->save();
                        }
                    }
                } else {

                    $this->comment('Nao existe list_transactions para #' . $sale->id . '  #' . $sale->hash_id);
                }

            } catch (Exception $e) {
                dd($e);
            }
        }

        $search->ended_at = now();
        $search->save();

        $this->comment('............');
        $this->comment(now()->format('H:i:s'));
    }
}
