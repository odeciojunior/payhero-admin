<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Product;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;

class UpdateTransactionsReleaseDate extends Command
{
    protected $signature = 'updateTransactionsReleaseDate';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        try {

            $getnetService = new GetnetBackOfficeService();

            $transactions = Transaction::with([
                                                  'company',
                                                  'user',
                                                  'sale' => function ($query) {
                                                      $query->withCount([
                                                                            'products as digital_products_count' => function ($query) {
                                                                                $query->where('products.type_enum', Product::TYPE_DIGITAL);
                                                                            }
                                                                        ]);
                                                  }
                                              ])->whereIn('gateway_id',
                                                          [Gateway::GETNET_SANDBOX_ID, Gateway::GETNET_PRODUCTION_ID])
                ->where('status_enum', Transaction::STATUS_PAID)
                ->whereNotNull('company_id')
                ->whereNull('release_date');

            $total = $transactions->count();

            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $transactions->chunk(1000, function ($transactions) use ($bar, $getnetService) {
                foreach ($transactions as $transaction) {
                    try {
                        $company = $transaction->company;
                        $user = $transaction->user;
                        $sale = $transaction->sale;

                        $statement = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                            ->setStatementSubSellerId($company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID))
                            ->getStatement($sale->gateway_order_id);

                        $getnetSale = json_decode($statement);

                        $getnetTransaction = $getnetSale->list_transactions[0] ?? null;
                        $details = $getnetTransaction ? $getnetTransaction->details[0] ?? null : null;

                        if (!empty($details)) {
                            $releaseDate = Carbon::parse($details->payment_date);
                            $releaseCount = $user->release_count + 1;
                            //se for produto digital, o prazo mínimo é 7 dias
                            if ($sale->digital_products_count) {
                                $transactionDate = Carbon::parse($details->transaction_date);
                                $transactionDate->setTimeFrom('00:00:00');
                                $diffInDays = $releaseDate->diffInDays($transactionDate);
                                if ($diffInDays < 7) {
                                    $releaseDate->addDays(7 - $diffInDays);
                                }
                            }
                            //se tem o benefício receba mais rápido
                            if ($user->get_faster) {
                                $transaction->tracking_required = false;
                            }
                            if ($user->has_security_reserve && is_null($transaction->invitation_id)) {
                                //reserva de segurança
                                if ($releaseCount == 20) {
                                    $releaseDate = now()->addDays(90);
                                    $transaction->is_security_reserve = true;
                                    $releaseCount = 0;
                                }
                                $user->release_count = $releaseCount;
                                $user->save();
                            }
                            $transaction->release_date = $releaseDate->toDateTimeString();
                            $transaction->save();
                        } else {
                            $getnetSale = $getnetService
                                ->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                                ->setStatementSubSellerId($company->getGatewaySubsellerId(Gateway::GETNET_PRODUCTION_ID))
                                ->getStatement($sale->gateway_order_id);

                            $getnetTransaction = $getnetSale->list_transactions[0] ?? null;
                            $details = $getnetTransaction ? $getnetTransaction->details[0] ?? null : null;
                            if (!empty($details)) {
                                $releaseDate = Carbon::parse($details->payment_date);
                                //se for produto digital, o prazo mínimo é 7 dias
                                if ($sale->digital_products_count) {
                                    $transactionDate = Carbon::parse($details->transaction_date);
                                    $transactionDate->setTimeFrom('00:00:00');
                                    $diffInDays = $releaseDate->diffInDays($transactionDate);
                                    if ($diffInDays < 7) {
                                        $releaseDate->addDays(7 - $diffInDays);
                                    }
                                }
                                $transaction->release_date = $releaseDate->toDateTimeString();
                                $transaction->save();
                            }
                        }

                    } catch (Exception $e) {
                        report($e);
                    }
                    $bar->advance();
                }
            });

            $bar->finish();

        } catch (Exception $e) {
            report($e);
        }

    }
}
