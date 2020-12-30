<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\GetnetBackOfficeService;
use Modules\Transfers\Services\GetNetStatementService;
use Vinkla\Hashids\Facades\Hashids;

class VerifyTransfersGetnet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:transfersgetnet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'routine responsible for transferring the available money from the transactions to the users company registered getnet account';

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
        $companyModel = new Company();
        $transactionModel = new Transaction();

        $gatewayIds = Gateway::whereIn(
            'name',
            [
                'getnet_sandbox',
                'getnet_production'
            ]
        )->get()->pluck('id')->toArray();

        $transactions = $transactionModel->with('sale')
            ->where('release_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('status_enum', $transactionModel->present()->getStatusEnum('paid'))
            ->whereHas(
                'sale',
                function ($query) use ($gatewayIds) {
                    $query->where(
                        function ($q) use ($gatewayIds) {
                            $q->where('has_valid_tracking', true)->orWhereNull('delivery_id');
                        }
                    )->whereIn('gateway_id', $gatewayIds);
                }
            );

        foreach ($transactions->cursor() as $transaction) {

            try {
                if (!empty($transaction->company_id)) {

                    $company = $companyModel->find($transaction->company_id);

                    if (in_array($transaction->sale->gateway_id, $gatewayIds)) {

                        $subSeller = $company->subseller_getnet_id;

                        $getNetBackOfficeService = new GetnetBackOfficeService();
                        $getNetBackOfficeService->setStatementSubSellerId($subSeller)
                            ->setStatementSaleHashId(Hashids::connection('sale_id')->encode($transaction->sale_id));

                        $originalResult = $getNetBackOfficeService->getStatement();
                        $result = json_decode($originalResult);

                        $transactionsGetNet = (new GetNetStatementService())->performStatement($result);

                        if (array_key_exists('items', $transactionsGetNet)) {

                            $transactionGetNet = collect($transactionsGetNet['items'])->first();

                            // TODO
                            /* essa condição existe porque na data de 08/12/2020 existem 304 vendas que na GETNET não vem
                            com details e consequentemente não vem no tratamento de (new GetNetStatementService())->performStatement($result);

                            Os registros são esses:
                            SELECT * FROM sales WHERE id IN (839269, 839333, 839386, 840169, 840233, 840355, 840403,
                            840653, 840982, 841245, 841362, 841650, 842845, 842905, 843220, 843294, 843690, 844008,
                            844509, 844519, 844519, 844527, 844527, 844634, 844634, 845207, 846032, 846098, 846098,
                            846194, 846194, 846555, 846555, 846727, 846727, 843170, 847022, 847022, 847125, 847125,
                            847236, 847236, 847413, 847413, 847438, 847550, 847550, 847680, 847818, 847818, 848026,
                            848026, 848054, 848054, 848167, 848167, 848210, 848352, 848652, 848652, 848693, 848693,
                            848718, 848718, 848761, 848761, 848965, 848965, 849062, 849062, 849216, 849309, 849309,
                            849324, 849324, 849498, 849498, 849540, 849602, 849602, 849779, 849885, 849885, 850082,
                            850082, 850216, 850216, 850228, 850258, 850258, 850331, 850331, 850348, 850348, 850444,
                            850444, 850543, 850543, 850590, 850590, 850661, 850661, 850803, 850803, 851854, 851917,
                            851917, 851969, 851969, 847306, 847306, 842118, 852271, 852271, 852363, 852363, 852465,
                            852465, 852551, 852551, 852579, 852579, 852667, 852667, 852711, 852711, 852809, 852809,
                            853074, 853074, 853082, 853082, 853608, 853608, 853702, 853702, 853755, 853755, 853788,
                            853788, 853966, 853966, 847649, 850385, 850385, 854050, 854050, 854077, 854077, 854098,
                            854098, 854112, 854112, 854195, 854195, 854246, 854246, 854342, 854342, 854748, 854748,
                            854772, 854774, 854808, 854879, 854879, 854949, 854949, 854999, 855074, 855074, 855077,
                            855143, 855196, 855356, 855369, 855369, 855375, 855422, 855422, 855487, 855487, 855538,
                            855550, 855550, 855603, 855767, 855774, 855774, 855810, 855852, 855852, 855854, 855875,
                            855887, 855887, 856023, 856023, 856075, 856248, 856269, 856269, 856277, 856277, 856316,
                            856316, 856325, 856349, 856389, 856478, 856529, 856605, 856707, 856707, 856838, 856838,
                            856892, 856892, 856901, 856901, 856992, 856992, 857003, 857028, 857028, 857064, 857354,
                            857617, 857617, 857633, 857633, 857754, 857818, 857868, 857868, 857886, 857922, 857922,
                            857953, 858008, 858085, 858085, 858107, 858107, 858116, 850344, 850344, 858163, 858323,
                            858434, 858664, 858756, 858882, 858983, 858983, 858995, 858995, 858933, 859149, 859149,
                            859208, 859208, 859299, 859299, 859391, 859391, 859664, 859664, 859832, 859832, 859891,
                            859891, 860072, 860072, 860114, 860114, 860157, 860157, 860187, 860190, 860234, 860234,
                            860451, 860564, 860821, 860821, 860914, 860914, 860931, 860931, 860939, 860939, 860955,
                            860955, 861189, 861189, 861282, 861285, 861285, 861407, 861528, 862022, 862022, 862544,
                            862544, 862626, 862626, 862944, 862944, 863167, 863167, 863451, 863451, 863506, 863506,
                            863691, 863691, 863745, 863745, 863812, 863812, 863925, 863925, 863965, 863965, 863980,
                            863980, 863989, 863989, 864145, 864145, 864170, 864170, 864296, 864296, 864399, 864744,
                            864744, 864749, 864749, 864859, 864859, 865214, 865342, 865391, 865401, 865446, 865446,
                            865580, 866015, 866015, 866162, 866197, 866238, 866258, 866287, 866287, 866314, 866314,
                            866439, 866439, 866480, 866487, 866487, 866541, 866772, 866772, 866793, 866793, 866861,
                            866861, 867386, 867470, 867470, 867471, 867560, 867778, 867816, 867911, 868053, 868281,
                            868547, 868592, 868949, 869291, 869619, 869647, 869708, 869774, 864462, 870073, 870280,
                            870307, 870391, 870584, 870602, 870614, 870625, 870719, 870733, 870926, 871085, 871175,
                            871683, 871746, 871771, 871786, 871877, 871984, 872018, 872785, 872801, 872918, 872995,
                            873057, 873070, 873103, 873110, 873115, 873206, 873334, 873437, 873499, 873516, 873838,
                            874311, 874321, 874398, 874576, 874898, 874955, 875083, 875217, 875304, 875467, 876289,
                            878219, 878298, 878386, 878885, 879238, 853810, 873058, 880400, 882679)
                            ORDER BY id ASC;
                            */

                            if (!$transactionGetNet) {

                                $this->info(' - NOT FOUND AFTER  (new GetNetStatementService())->performStatement(...) | sale_id = ' . $transaction->sale->id);

                                /*if ($transaction->sale->created_at > '2020-10-30 13:28:51.0') {

                                    $orderId = $transaction->sale->hash_id . '-' . $transaction->sale->id . '-' . $transaction->sale->attempts;
                                } else {

                                    $orderId = $transaction->sale->hash_id . '-' . $transaction->sale->attempts;
                                }*/

                                //print_r($orderId . ', ');
                                //print_r($transaction->sale->id.', ');
                                //print_r($transaction->sale->hash_id.', ');
                            } else {

                                if (!empty($transactionGetNet->subSellerRateConfirmDate)) {

                                    $this->info(' - UPDATE - ' . $transactionGetNet->order->getSaleId() . ' :: ' . $transactionGetNet->details->getStatus() . ' :: subSellerRateConfirmDate = ' . $transactionGetNet->subSellerRateConfirmDate);

                                    $transaction->update(
                                        [
                                            'status' => 'transfered',
                                            'status_enum' => $transactionModel->present()->getStatusEnum('transfered'),
                                        ]
                                    );
                                } else {

                                    $this->comment(' - ' . $transactionGetNet->order->getSaleId() . ' :: ' . $transactionGetNet->details->getStatus());

                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                report($e);
            }
        }
    }
}
