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
            $this->captureInvitonErro();
        } catch (Exception $e) {
            dd($e);
        }
    }

    public  function  captureInvitonErro() {
        try {

            //$saleIds = $this->diffArrayUm($this->saleIdsUpdate(), $this->saleIdsCommnad());
            $saleIds = $this->saleIdsCommnad();

            $sales = Sale::
            with('transactions')
                ->whereHas('transactions', function ($query) {
                    $query->where('type', Transaction::TYPE_INVITATION);
                    $query->where('gateway_id', Gateway::GETNET_PRODUCTION_ID);
                    $query->whereIn('status_enum', [Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED]);
                })
                //->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
                ->whereDate('created_at', '<=', '2021-12-31')
                ->whereIn('id', $saleIds);

            $total = $sales->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $getnetService = new GetnetBackOfficeService();
            $aux = 0;

            $arrayInvitation  = [];
            $valueTotal = 0;
            foreach ($sales->cursor() as $sale) {

                if ($aux == 100) {
                    $getnetService = new GetnetBackOfficeService();
                    $aux = 0;
                    sleep(1);
                }
                $aux ++;

                $orderId = $sale->gateway_order_id;
                $transaction = $sale->transactions()->first();

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
                    //$this->line(" {$transaction->id} não possui detalhes na getnet - " . $sale->id . " - User: " . $transaction->user->id . " - " . $transaction->user->name);

                    $isWithdrawal = 'notWithdrawal';
                    if(!empty($transaction->withdrawal_id)) {

                        $isWithdrawal = 'isWithdrawal';
                        $this->line("Tem saque");
//                        \Log::info("Saque {$transaction->withdrawal->id}, {$transaction->withdrawal->status}, {$transaction->withdrawal->is_released}");
//                        $transaction->withdrawal->update(
//                            [
//                                'status' => Withdrawal::STATUS_PARTIALLY_LIQUIDATED,
//                                'is_released' => 0,
//                            ]
//                        );
                    }
                    //$transaction->withdrawal->update(['status' => Withdrawal::STATUS_PARTIALLY_LIQUIDATED]);

                    if (isset($arrayInvitation[$transaction->user->id])) {

                        $arrayInvitation[$transaction->user->id]['count_sale'] += 1;
                        $arrayInvitation[$transaction->user->id]['value'][$isWithdrawal] += $transaction->value;
                        array_push($arrayInvitation[$transaction->user->id]['sale_ids'][$isWithdrawal], $transaction->sale_id);
                        array_push($arrayInvitation[$transaction->user->id]['values'][$isWithdrawal], $transaction->value);
                    } else {

                        $arrayInvitation[$transaction->user->id] = [
                            'user_id' => $transaction->user->id,
                            'user_name' => $transaction->user->name,
                            'company_id' => $transaction->company->id,
                            'company_name' => $transaction->company->fantasy_name,
                            'count_sale' => 1,
                            'value' => [
                                $isWithdrawal => $transaction->value
                            ],
                            'sale_ids' => [
                                $isWithdrawal => [$transaction->sale_id]
                            ],
                            'values' => [
                                $isWithdrawal => [$transaction->value]
                            ]
                        ];

                    }

                    $valueTotal += $transaction->value;

                } else {
                    $this->line(" {$transaction->id} possui detalhes na getnet - " . $sale->id . " - User: " . $transaction->user->id . " - " . $transaction->user->name);
                }

                $bar->advance();

            }

            dd($arrayInvitation, $valueTotal);

            $bar->finish();

        } catch (Exception $e) {
            dd($e);
        }
    }


    function diff( $ary_1, $ary_2 ) {
        // compare the value of 2 array
        // get differences that in ary_1 but not in ary_2
        // get difference that in ary_2 but not in ary_1
        // return the unique difference between value of 2 array
        $diff = array();

        // get differences that in ary_1 but not in ary_2
        foreach ( $ary_1 as $v1 ) {
            $flag = 0;
            foreach ( $ary_2 as $v2 ) {
                $flag |= ( $v1 == $v2 );
                if ( $flag ) break;
            }
            if ( !$flag ) array_push( $diff, $v1 );
        }

        // get difference that in ary_2 but not in ary_1
        foreach ( $ary_2 as $v2 ) {
            $flag = 0;
            foreach ( $ary_1 as $v1 ) {
                $flag |= ( $v1 == $v2 );
                if ( $flag ) break;
            }
            if ( !$flag && !in_array( $v2, $diff ) ) array_push( $diff, $v2 );
        }

        return $diff;
    }

    public  function  AjustInviton() {
        try {
            //dd($this->saleIdsUpdate());
            //dd($this->diffArrayUm($this->saleIdsUpdate(), $this->saleIdsCommnad()));
            $saleIds = $this->diffArrayUm($this->saleIdsUpdate(), $this->saleIdsCommnad());

            //dd(count($saleIds));

            //$saleIds = $this->saleIds();
            $sales = Sale::
            with('transactions')
                ->whereHas('transactions', function ($query) {
                    $query->where('type', Transaction::TYPE_INVITATION);
                    $query->whereIn('status_enum', [Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID]);
                })
                ->where('gateway_id', Gateway::GETNET_PRODUCTION_ID)
                ->whereIn('id', $saleIds);

            //dd(count($sales));
//            $diff2 = array();
//            $diff3 = array();
//            foreach ( $saleIds as $saleId ) {
//
//                $flag = 0;
//                $i = 0;
//                foreach ( $saleIds as $saleId2 ) {
//                    //$flag |= ( $v1 == $v2 );
//                    //if ( $flag ) break;
//                    if ($saleId == $saleId2) {
//                        $i ++;
//                        //dd($saleId, $sale->id);
//                        if( $i > 1) {
//                            array_push($diff3, $saleId);
//                            $flag = 1;
//                            //break;
//                        }
//
//                    }
//                }
////                if ( !$flag ) {
////                    dd($saleId);
////                    array_push($diff2, $saleId);
////                }
//
////                if(in_array($sale->id, $saleIds)) {
////                    //$this->line($v2);
////                    //array_push( $diff, $v1 );
////                    $diff2[] = $sale->id;
////                }
//            }
//
//            dd($diff3);

            $total = $sales->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            $getnetService = new GetnetBackOfficeService();
            $aux = 0;
            $aux2 = 0;
            foreach ($sales->cursor() as $sale) {
                $this->line($sale->id);
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
                //$transaction = $sale->transactions()->first();
                $transaction = $sale->transactions()->where('type', Transaction::TYPE_INVITATION)->first();
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
                    //\Log::info("{$transaction->id} não possui detalhes na getnet - " . $sale->id);
                    $this->line(" {$transaction->id} não possui detalhes na getnet - " . $sale->id . " - User: " . $transaction->user->id . " - " . $transaction->user->name);

                } else if (
                    isset($getnetSale->list_transactions) &&
                    isset($getnetSale->list_transactions[0]) &&
                    isset($getnetSale->list_transactions[0]->details) &&
                    isset($getnetSale->list_transactions[0]->details[0]) &&
                    isset($getnetSale->list_transactions[0]->details[0]->release_status)
                ) {
                    if ($getnetSale->list_transactions[0]->details[0]->release_status == 'S') {
                        //$countTransactionsReleased++;
                        $this->line(" {$transaction->id}  enviado - " . $sale->id);
                    } elseif ($getnetSale->list_transactions[0]->details[0]->release_status == 'N') {
                        //event(new CheckTransactionReleasedEvent($transaction->id));
                        $this->line(" {$transaction->id} não enviado - {$sale->id} - {$transaction->withdrawal_id}");
                        if(!empty($transaction->withdrawal_id)) {
                            $this->line("Tem saque");

                            if($transaction->withdrawal->status == Withdrawal::STATUS_TRANSFERRED) {
                                \Log::info("Saque {$transaction->withdrawal->id}, {$transaction->withdrawal->status}, {$transaction->withdrawal->is_released}");
                                $transaction->withdrawal->update(
                                    [
                                        'status' => Withdrawal::STATUS_PARTIALLY_LIQUIDATED,
                                        'is_released' => 0,
                                    ]
                                );
                            }
                            //$transaction->withdrawal->update(['status' => Withdrawal::STATUS_PARTIALLY_LIQUIDATED]);
                        } else {
                            $this->line("Não Tem saque");

                        }

                        \Log::info("Transaction {$transaction->id}, {$transaction->gateway_released_at}, {$transaction->gateway_transferred_at}, {$transaction->gateway_transferred}");
                        $transaction->update([
                                                 'gateway_released_at' => null,
                                                 'gateway_transferred_at' => null,
                                                 'gateway_transferred' => 0,
                                             ]);
//                        $transaction->withdrawal->update(['status' => Withdrawal::STATUS_PARTIALLY_LIQUIDATED]);
                    }
                }

                $aux2 ++;

                $bar->advance();

            }

            $bar->finish();

        } catch (Exception $e) {
            dd($e);
        }
    }

    function diffArrayUm( $ary_1, $ary_2 ) {

        $diff = array();
        foreach ( $ary_1 as $v1 ) {
//            $flag = 0;
//            foreach ( $ary_2 as $v2 ) {
//                //$flag |= ( $v1 == $v2 );
//                //if ( $flag ) break;
//
//                if ( $v1 == $v2 ) {
//                    $flag = 1;
//                    break;
//                }
//            }
//            if ( !$flag ) array_push( $diff, $v1 );

            if(!in_array($v1, $ary_2)) {
                //$this->line($v2);
                //array_push( $diff, $v1 );
                $diff[] = $v1;
            }
        }

        return $diff;
    }

    public function checkInvionGetNet()
    {
        try {

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

                $response = $getnetService->setStatementSaleHashId(hashids_encode($sale->id, 'sale_id'))
                    ->setStatementSubSellerId(CompanyService::getSubsellerId($transaction->company))
                    ->getStatement($orderId);

                $getnetSale = json_decode($response);

                if (
                    !isset($getnetSale->list_transactions) ||
                    !isset($getnetSale->list_transactions[0]) ||
                    !isset($getnetSale->list_transactions[0]->details)
                ) {
                    \Log::info("{$transaction->id} não possui detalhes na getnet - " . $sale->id);
                    $this->line(" {$transaction->id} não possui detalhes na getnet - " . $sale->id . " - User: " . $transaction->user->id . " - " . $transaction->user->name);
                }

                $aux2 ++;

                $bar->advance();

            }

            $bar->finish();

        } catch (Exception $e) {
            dd($e);
        }
    }

    //Convites que setamos como transferido
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

    //Convites que não gerarm os detalhes na getnet
    public function  saleIdsCommnad()
    {
//        return [
//            1303532,
//            1306178,
//            1306186,
//            1306191,
//            1306203,
//            1306205,
//            1306207
//        ];
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

    //Diferença entre os array
    public function arrayDiff() {
        return [
            1306472,
            1306640,
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
            1355281
        ];

    }

}
