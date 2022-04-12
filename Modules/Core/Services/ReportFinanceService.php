<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Sale;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\Company;
use Vinkla\Hashids\Facades\Hashids;
use Modules\Core\Entities\Cashback;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Services\Gateways\AsaasService;
use Modules\Core\Services\Gateways\CieloService;
use Modules\Core\Services\Gateways\GerencianetService;
use Modules\Core\Services\Gateways\GetnetService;
use Modules\Core\Services\Gateways\Safe2PayService;

class ReportFinanceService
{
    function getFinancesResume($filters)
    {
        try {
            $transactionModel = new Transaction();
            $companyModel = new Company();

            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $userId = auth()->user()->account_owner_id;
            $userCompanies = $companyModel->where('user_id', $userId)->get()->pluck('id')->toArray();

            $transactions = $transactionModel
            ->whereIn('company_id', $userCompanies)
            ->join('sales', 'sales.id', 'transactions.sale_id')
            ->whereBetween('start_date', [ $dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59' ])
            ->whereNull('invitation_id');

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $transactions->where('project_id', $projectId);
            }

            $queryCount = $transactions->count();

            $queryAverageTicket = $transactions->avg('transactions.value');

            $queryComission = $transactions
            ->whereIn('status_enum', [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ])
            ->whereIn('sales.status', [ 1, 2, 4, 7, 8, 12, 20, 21, 22 ])
            ->sum('transactions.value');

            $transactions = $transactionModel
            ->whereIn('company_id', $userCompanies)
            ->join('sales', 'sales.id', 'transactions.sale_id')
            ->whereBetween('start_date', [ $dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59' ])
            ->whereNull('invitation_id')
            ->whereIn('sales.status', [ 1, 2, 4, 7, 8, 12, 20, 21, 22 ])
            ->whereIn('status_enum', [ Transaction::STATUS_PAID, Transaction::STATUS_TRANSFERRED ]);

            if (!empty($filters["project"])) {
                $projectId = current(Hashids::decode($filters["project"]));
                $transactions->where('project_id', $projectId);
            }

            $queryChargeback = $transactions
            ->where('status_enum', Transaction::STATUS_CHARGEBACK)
            ->where('sales.status', Sale::STATUS_CHARGEBACK)
            ->sum('transactions.value');

            return [
                'transactions' => $queryCount,
                'average_ticket' => foxutils()->formatMoney($queryAverageTicket / 100),
                'comission' => foxutils()->formatMoney($queryComission / 100),
                'chargeback' => foxutils()->formatMoney($queryChargeback / 100)
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesCashbacks($filters)
    {
        try {
            $dateRange = foxutils()->validateDateRange($filters["date_range"]);
            $userId = auth()->user()->account_owner_id;

            $cashbacks = Cashback::where('user_id', $userId)->whereBetween('created_at', [$dateRange[0].' 00:00:00', $dateRange[1].' 23:59:59']);

            $cashbacksValue = $cashbacks->sum('value');
            $cashbacksCount = $cashbacks->count();

            return [
                'value' => foxutils()->formatMoney($cashbacksValue / 100),
                'quantity' => $cashbacksCount
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesPendings()
    {
        try {
            $defaultGateways = [
                Safe2PayService::class,
                AsaasService::class,
                GetnetService::class,
                GerencianetService::class,
                CieloService::class,
            ];

            $balancesPendingValue = [];
            $balancesPendingCount = [];

            $companies = Company::where('user_id', auth()->user()->account_owner_id)->get();
            foreach($companies as $company) {
                foreach($defaultGateways as $gatewayClass) {
                    $gateway = app()->make($gatewayClass);
                    $gateway->setCompany($company);

                    $balancesPendingValue[] = $gateway->getPendingBalance();
                    $balancesPendingCount[] = $gateway->getPendingBalanceCount();
                }
            }

            $totalPendingValue = array_sum($balancesPendingValue);
            $totalPendingCount = array_sum($balancesPendingCount);

            return [
                'value' => foxutils()->formatMoney($totalPendingValue / 100),
                'amount' => $totalPendingCount
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesBlockeds()
    {
        try {
            $defaultGateways = [
                Safe2PayService::class,
                AsaasService::class,
                GetnetService::class,
                GerencianetService::class,
                CieloService::class,
            ];

            $balancesBlockedValue = [];
            $balancesBlockedCount = [];

            $balancesBlockedPendingValue = [];
            $balancesBlockedPendinCount = [];

            $companies = Company::where('user_id', auth()->user()->account_owner_id)->get();
            foreach($companies as $company) {
                foreach($defaultGateways as $gatewayClass) {
                    $gateway = app()->make($gatewayClass);
                    $gateway->setCompany($company);

                    $balancesBlockedValue[] = $gateway->getBlockedBalance();
                    $balancesBlockedCount[] = $gateway->getBlockedBalanceCount();

                    $balancesBlockedPendingValue[] = $gateway->getBlockedBalancePending();
                    $balancesBlockedPendinCount[] = $gateway->getBlockedBalancePendingCount();
                }
            }

            $totalBlockedValue = array_sum($balancesBlockedValue) + array_sum($balancesBlockedPendingValue);
            $totalBlockedCount = array_sum($balancesBlockedCount) + array_sum($balancesBlockedPendinCount);

            return [
                'value' => foxutils()->formatMoney($totalBlockedValue / 100),
                'amount' => $totalBlockedCount
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    function getFinancesDistribuitions()
    {
        try {
            $defaultGateways = [
                Safe2PayService::class,
                AsaasService::class,
                GetnetService::class,
                GerencianetService::class,
                CieloService::class,
            ];

            $balancesAvailable = [];
            $balancesPending = [];
            $balancesBlocked = [];
            $balancesBlockedPending = [];

            //$balancesPendingCount = [];
            //$balancesBlockedCount = [];
            //$balancesBlockedPendingCount = [];

            $companies = Company::where('user_id', auth()->user()->account_owner_id)->get();
            foreach($companies as $company) {
                foreach($defaultGateways as $gatewayClass) {
                    $gateway = app()->make($gatewayClass);
                    $gateway->setCompany($company);

                    $balancesAvailable[] = $gateway->getAvailableBalance();
                    $balancesPending[] = $gateway->getPendingBalance();
                    $balancesBlocked[] = $gateway->getBlockedBalance();
                    $balancesBlockedPending[] = $gateway->getBlockedBalancePending();

                    //$balancesPendingCount[] = $gateway->getPendingBalanceCount();
                    //$balancesBlockedCount[] = $gateway->getBlockedBalanceCount();
                    //$balancesBlockedPendingCount[] = $gateway->getBlockedBalancePendingCount();
                }
            }

            $availableBalance = array_sum($balancesAvailable);
            $pendingBalance = array_sum($balancesPending);
            $blockedBalance = array_sum($balancesBlocked);
            $blockedBalancePending = array_sum($balancesBlockedPending);

            //$pendingBalanceCount = array_sum($balancesPendingCount);
            //$blockedBalanceCount = array_sum($balancesBlockedCount);
            //$blockedBalancePendingCount = array_sum($balancesBlockedPendingCount);

            $totalBalance = ($availableBalance + $pendingBalance + $blockedBalance + $blockedBalancePending);

            return [
                'available' => [
                    'value' => foxutils()->formatMoney($availableBalance / 100),
                    'percentage' => round(($availableBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP),
                    'color' => 'green'
                ],
                'pending' => [
                    'value' => foxutils()->formatMoney($pendingBalance / 100),
                    'percentage' => round(($pendingBalance * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP),
                    'color' => 'yellow'
                ],
                'blocked' => [
                    'value' => foxutils()->formatMoney(($blockedBalance + $blockedBalancePending) / 100),
                    'percentage' => round((($blockedBalance + $blockedBalancePending) * 100) / $totalBalance, 1, PHP_ROUND_HALF_UP),
                    'color' => 'red'
                ],
                'total' => foxutils()->formatMoney($totalBalance / 100),
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getFinancesWithdrawals()
    {
        try {
            date_default_timezone_set('America/Sao_Paulo');

            $dateEnd = date('Y-m-d');
            $dateStart = date('Y-m-d', strtotime($dateEnd . ' -5 month'));

            $companies = Company::where('user_id', auth()->user()->account_owner_id)->get()->pluck('id')->toArray();

            $withdrawals = Withdrawal::whereIn('company_id', $companies)->whereBetween('release_date', [ $dateStart.' 00:00:00', $dateEnd.' 23:59:59' ]);

            $transactions = Transaction::whereIn('transactions.company_id', $companies)
            ->join('sales', 'transactions.sale_id', 'sales.id')
            ->whereNotIn('sales.status', [
                Sale::STATUS_CANCELED_ANTIFRAUD,
                Sale::STATUS_REFUSED,
                Sale::STATUS_SYSTEM_ERROR
            ])
            ->whereBetween('sales.start_date', [ $dateStart.' 00:00:00', $dateEnd.' 23:59:59' ]);

            $dateStart = Carbon::parse($dateStart);
            $dateEnd = Carbon::parse($dateEnd);

            $labelList = [];
            while ($dateStart->lessThanOrEqualTo($dateEnd)) {
                array_push($labelList, $dateStart->format('M'));
                $dateStart = $dateStart->addMonths(1);
            }

            $resumeWithdrawals = $withdrawals
            ->select(DB::raw('value, DATE(release_date) as date'))
            ->get();

            $resumeTransactions = $transactions
            ->select(DB::raw('sales.original_total_paid_value, DATE(sales.start_date) as date'))
            ->get();

            $withdrawalData = [];
            $transactionData = [];

            $labelList = array_reverse($labelList);
            foreach ($labelList as $label) {
                $withdrawalDataValue = 0;
                $transactionDataValue = 0;

                foreach ($resumeWithdrawals as $r) {
                    if (Carbon::parse($r->date)->format('M') == $label) {
                        $withdrawalDataValue += intval(preg_replace("/[^0-9]/", "", $r->value));
                    }
                }

                foreach ($resumeTransactions as $r) {
                    if (Carbon::parse($r->date)->format('M') == $label) {
                        $transactionDataValue += intval(preg_replace("/[^0-9]/", "", $r->original_total_paid_value));
                    }
                }

                array_push($withdrawalData, $withdrawalDataValue);
                array_push($transactionData, $transactionDataValue);
            }

            $totalWithdrawal = array_sum($withdrawalData);
            $totalTransactions = array_sum($transactionData);

            return [
                'chart' => [
                    'labels' => $labelList,
                    'withdrawal' => [
                        'values' => $withdrawalData,
                        'total' => foxutils()->formatMoney($totalWithdrawal / 100)
                    ],
                    'income' => [
                        'values' => $transactionData,
                        'total' => foxutils()->formatMoney($totalTransactions / 100)
                    ]
                ]
            ];
        } catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
