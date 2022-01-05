<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Task;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Events\CheckTransactionReleasedEvent;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Core\Services\TaskService;

use function Clue\StreamFilter\fun;

class CheckInvitationGetnet extends Command
{
    protected $signature = 'check:invitation-getnet';

    protected $description = 'Command description';
    //sem detalhes compensar asaas
    //com detalhes tornat disponivel

    public function __construct()
    {
        parent::__construct();
    }

    public  function  handle() {
        try {

            //$saleIds = $this->saleIds();
            $sales = Sale::with('transactions')
                ->whereHas('transactions', function ($query) {
                    $query->where('type', Transaction::TYPE_INVITATION);
                    $query->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
                    $query->whereIn('status_enum', [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED]);
                    $query->whereDate('created_at', '>=', '2022-01-01');
                    $query->whereDate('created_at', '<=', '2022-01-31');
                });
                //->where('id', 1365721);
                //->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
                //->whereIn('id', $saleIds);



            $total = $sales->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $getnetService = new GetnetBackOfficeService();
            $aux = 0;
            $aux2 = 0;
            foreach ($sales->cursor() as $sale) {

                if ($aux2 == 4) {
                    sleep(1);
                    $aux2 = 0;
                }

                if ($aux == 100) {
                    $getnetService = new GetnetBackOfficeService();
                    $aux = 0;
                    sleep(1);
                }
                $aux ++;

                $orderId = $sale->gateway_order_id;
                $transaction = $sale->transactions()->first();
                //dd(CompanyService::getSubsellerId($transaction->company));
                //$this->line($transaction->created_at);

                $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                    ->setStatementSubSellerId(CompanyService::getSubsellerId($transaction->company))
                    ->getStatement($orderId);

                $getnetSale = json_decode($response);
                //dd($getnetSale->list_transactions);

                if (
                    !isset($getnetSale->list_transactions) ||
                    !isset($getnetSale->list_transactions[0]) ||
                    !isset($getnetSale->list_transactions[0]->details)
                ) {
                    \Log::info("{$transaction->id} não possui detalhes na getnet - " . $sale->id);
                    $this->line(" {$transaction->id} não possui detalhes na getnet - " . $sale->id . " - User: " . $transaction->user->id . " - " . $transaction->user->name);

                } else {

                }

                $aux2 ++;

                $bar->advance();

            }

            $bar->finish();

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function handle2()
    {
        try {

            $saleIds = $this->saleIds();
            $sales = Sale::with('transactions')
                ->whereHas('transactions', function ($query) {
                    $query->where('type', Transaction::TYPE_INVITATION);
                })
                ->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
                ->whereIn('id', $saleIds);

            $getnetService = new GetnetBackOfficeService();
            $aux = 0;
            foreach ($sales->cursor() as $sale) {
                if($aux == 100) {
                    $getnetService = new GetnetBackOfficeService();
                    $aux = 0;
                }
                $aux ++;

                $orderId = $sale->gateway_order_id;
                $transaction = $sale->transactions()->first();
                //dd($transaction->user_id);

                $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                    ->setStatementSubSellerId(CompanyService::getSubsellerId($transaction->company))
                    ->getStatement($orderId);

                $getnetSale = json_decode($response);

                if (
                    !isset($getnetSale->list_transactions) ||
                    !isset($getnetSale->list_transactions[0]) ||
                    !isset($getnetSale->list_transactions[0]->details)
                ) {
                    //\Log::info("{$transaction->id} não possui detalhes na getnet - " . $sale->id);
                    //$this->line("{$transaction->id} não possui detalhes na getnet - " . $sale->id . " - User: " . $transaction->user->id . " - " . $transaction->user->name);
                } else {
                    //\Log::info($getnetSale->list_transactions[0]->summary->reason_message . ' - ' . $sale->id);
                    //$this->line($getnetSale->list_transactions[0]->summary->reason_message . ' - ' . $sale->id . " - User: " . $transaction->user->id . " - " . $transaction->user->name);

                    if ($sale->status == Sale::STATUS_APPROVED) continue;
                    $this->line($sale->id . ',');
                    continue;
                    if ($sale->status == Sale::STATUS_CHARGEBACK) {

                        if (
                            isset($gatewaySale->list_transactions) &&
                            isset($gatewaySale->list_transactions[0]) &&
                            isset($gatewaySale->list_transactions[0]->details) &&
                            isset($gatewaySale->list_transactions[0]->details[0]) &&
                            isset($gatewaySale->list_transactions[0]->details[0]->release_status)
                        ) {
                            if ($gatewaySale->list_transactions[0]->details[0]->release_status == 'N') {
                                event(new CheckTransactionReleasedEvent($transaction->id));
                            }
                        }
                    } else {

                        $details = $getnetSale->list_transactions[0]->details;

                        if(!empty($details[0]->subseller_rate_closing_date) && empty($details[0]->subseller_rate_confirm_date)) {
                            Log::info("{$transaction->sale->id} está com closing_date e sem confirm_date");
                        }
                        else {
                            Log::info("{$transaction->sale->id} está com algum outro problema");
                        }
                    }
                }
            }


//            $withdrawals = Withdrawal::with('transactions', 'transactions.sale', 'transactions.company')
//                ->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
//                ->where('automatic_liquidation', true)
//                ->whereIn('status', [Withdrawal::STATUS_LIQUIDATING, Withdrawal::STATUS_PARTIALLY_LIQUIDATED])
//                ->orderBy('id');
//
//            $withdrawals->chunk(500, function ($withdrawals) {
//                foreach ($withdrawals as $withdrawal) {
//                    $getnetService = new GetnetBackOfficeService();
//
//                    $withdrawalTransactionsCount = $withdrawal->transactions->count();
//                    $countTransactionsLiquidated = 0;
//
//                    foreach ($withdrawal->transactions as $transaction) {
//                        $sale = $transaction->sale;
//
//                        if (!empty($transaction->gateway_transferred_at)) {
//                            $countTransactionsLiquidated++;
//                            continue;
//                        }
//
//                        $orderId = $sale->gateway_order_id;
//
//                        $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
//                            ->setStatementSubSellerId(CompanyService::getSubsellerId($transaction->company))
//                            ->getStatement($orderId);
//
//                        $gatewaySale = json_decode($response);
//
//                        if (
//                            !empty($gatewaySale->list_transactions[0]) &&
//                            !empty($gatewaySale->list_transactions[0]->details[0]) &&
//                            !empty($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date)
//                        ) {
//                            $countTransactionsLiquidated++;
//
//                            $date = Carbon::parse($gatewaySale->list_transactions[0]->details[0]->subseller_rate_confirm_date);
//
//                            if (empty($transaction->gateway_transferred_at)) {
//                                $transaction->update([
//                                                         'status' => 'transfered',
//                                                         'status_enum' => Transaction::STATUS_TRANSFERRED,
//                                                         'gateway_transferred' => true,
//                                                         'gateway_transferred_at' => $date
//                                                     ]);
//                            }
//                        }
//                    }
//
//                    if ($countTransactionsLiquidated == $withdrawalTransactionsCount) {
//                        $withdrawal->update(['status' => Withdrawal::STATUS_TRANSFERRED]);
//
//                        TaskService::setCompletedTask(
//                            User::find($sale->owner_id),
//                            Task::find(Task::TASK_FIRST_WITHDRAWAL)
//                        );
//                    } elseif ($countTransactionsLiquidated > 0) {
//                        $withdrawal->update(['status' => Withdrawal::STATUS_PARTIALLY_LIQUIDATED]);
//                    }
//                }
//            });
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function  saleIdsUpdate() {
        return [
            1306241,
            1306249,
            1306374,
            1306402,
            1306442,
            1306459,
            1306482,
            1306514,
            1306599,
            1306657,
            1306712,
            1306718,
            1306749,
            1306789,
            1306836,
            1306850,
            1306891,
            1306961,
            1307016,
            1307077,
            1307082,
            1307193,
            1307217,
            1307220,
            1307237,
            1307265,
            1307347,
            1307426,
            1307449,
            1307518,
            1307575,
            1307626,
            1307686,
            1307751,
            1307775,
            1307872,
            1308021,
            1308104,
            1308161,
            1308302,
            1308368,
            1306178,
            1306186,
            1306211,
            1303532,
            1306240,
            1306271,
            1306286,
            1306321,
            1306324,
            1306331,
            1306332,
            1306345,
            1306366,
            1306369,
            1306387,
            1306388,
            1306397,
            1306472,
            1306494,
            1306518,
            1306565,
            1306579,
            1306584,
            1306630,
            1306640,
            1306649,
            1306781,
            1306841,
            1306908,
            1306913,
            1306922,
            1306932,
            1306940,
            1307143,
            1307186,
            1307226,
            1307336,
            1307339,
            1307502,
            1307581,
            1307793,
            1307796,
            1307869,
            1308234,
            1308251,
            1308266,
            1308328,
            1308372,
            1344508,
            1343986,
            1344282,
            1346346,
            1349383,
            1349874,
            1350386,
            1350493,
            1346591,
            1346951,
            1347460,
            1347622,
            1348083,
            1352886,
            1343826,
            1344508,
            1349137,
            1349258,
            1351530,
            1356775,
            1349137,
            1349258,
            1351530,
            1349251,
            1346211,
            1347095,
            1351337,
            1349182,
            1354018,
            1198350,
            1203239,
            1205123,
            1206211,
            1206643,
            1212853,
            1216499,
            1218713,
            1219021,
            1219056,
            1220458,
            1220803,
            1221230,
            1233646,
            1236069,
            1238288,
            1239005,
            1239007,
            1240102,
            1240991,
            1241512,
            1242344,
            1246697,
            1247988,
            1249593,
            1254625,
            1255234,
            1256068,
            1256674,
            1256810,
            1256854,
            1257203,
            1257486,
            1257531,
            1258048,
            1259813,
            1259900,
            1259940,
            1261396,
            1261403,
            1265593,
            1350613,
            1355281,
        ];
    }

    public function  saleIdsCommnad()
    {
        return [
            1303532,
            1306178,
            1306186,
            1306191,
            1306203,
            1306205,
            1306207,
            1306208,
            1306211,
            1306214,
            1306227,
            1306231,
            1306240,
            1306241,
            1306242,
            1306249,
            1306271,
            1306280,
            1306282,
            1306283,
            1306286,
            1306291,
            1306309,
            1306313,
            1306321,
            1306324,
            1306331,
            1306332,
            1306339,
            1306342,
            1306345,
            1306351,
            1306356,
            1306366,
            1306369,
            1306374,
            1306387,
            1306388,
            1306392,
            1306397,
            1306398,
            1306401,
            1306402,
            1306423,
            1306426,
            1306431,
            1306434,
            1306439,
            1306442,
            1306459,
            1306474,
            1306482,
            1306491,
            1306494,
            1306514,
            1306518,
            1306534,
            1306548,
            1306552,
            1306558,
            1306563,
            1306565,
            1306573,
            1306579,
            1306581,
            1306584,
            1306587,
            1306597,
            1306598,
            1306599,
            1306607,
            1306612,
            1306614,
            1306619,
            1306622,
            1306624,
            1306630,
            1306636,
            1306641,
            1306649,
            1306657,
            1306658,
            1306660,
            1306667,
            1306669,
            1306670,
            1306699,
            1306704,
            1306712,
            1306718,
            1306719,
            1306728,
            1306746,
            1306749,
            1306772,
            1306781,
            1306789,
            1306808,
            1306824,
            1306827,
            1306834,
            1306836,
            1306839,
            1306841,
            1306850,
            1306875,
            1306891,
            1306902,
            1306908,
            1306913,
            1306922,
            1306932,
            1306940,
            1306941,
            1306955,
            1306961,
            1306976,
            1306996,
            1307016,
            1307059,
            1307060,
            1307061,
            1307077,
            1307082,
            1307083,
            1307088,
            1307099,
            1307106,
            1307107,
            1307134,
            1307143,
            1307147,
            1307176,
            1307182,
            1307186,
            1307193,
            1307206,
            1307215,
            1307217,
            1307220,
            1307226,
            1307237,
            1307238,
            1307265,
            1307288,
            1307293,
            1307317,
            1307336,
            1307339,
            1307347,
            1307351,
            1307365,
            1307374,
            1307376,
            1307384,
            1307396,
            1307410,
            1307419,
            1307426,
            1307440,
            1307449,
            1307465,
            1307471,
            1307480,
            1307482,
            1307484,
            1307487,
            1307490,
            1307493,
            1307501,
            1307502,
            1307518,
            1307551,
            1307553,
            1307561,
            1307568,
            1307569,
            1307575,
            1307581,
            1307584,
            1307596,
            1307601,
            1307607,
            1307613,
            1307626,
            1307640,
            1307644,
            1307657,
            1307665,
            1307671,
            1307677,
            1307686,
            1307696,
            1307699,
            1307711,
            1307723,
            1307727,
            1307751,
            1307761,
            1307773,
            1307775,
            1307778,
            1307788,
            1307793,
            1307796,
            1307801,
            1307814,
            1307833,
            1307851,
            1307860,
            1307869,
            1307871,
            1307872,
            1307898,
            1307905,
            1307935,
            1307943,
            1307960,
            1307970,
            1307974,
            1307977,
            1307985,
            1307992,
            1308021,
            1308031,
            1308059,
            1308066,
            1308072,
            1308077,
            1308081,
            1308104,
            1308138,
            1308140,
            1308146,
            1308161,
            1308180,
            1308186,
            1308190,
            1308206,
            1308207,
            1308209,
            1308213,
            1308234,
            1308239,
            1308241,
            1308248,
            1308251,
            1308262,
            1308266,
            1308271,
            1308272,
            1308284,
            1308302,
            1308303,
            1308328,
            1308336,
            1308342,
            1308368,
            1308370,
            1308372,
            1308380,
            1308384,
            1308412,
            1308419
        ];
    }

}
