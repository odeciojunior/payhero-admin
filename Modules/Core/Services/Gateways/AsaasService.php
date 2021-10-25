<?php

namespace Modules\Core\Services\Gateways;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\StatementService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class AsaasService implements Statement
{
    public Company $company;
    public $gatewayIds = [];

    public function __construct()
    {
        $this->gatewayIds = [
            Gateway::ASAAS_PRODUCTION_ID,
            Gateway::ASAAS_SANDBOX_ID
        ];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function getAvailableBalance() : int
    {
        return $this->company->asaas_balance;
    }

    public function getPendingBalance() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->where('status_enum', Transaction::STATUS_PAID)
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('is_waiting_withdrawal', 0)
                            ->whereNull('withdrawal_id')
                            ->where('created_at', '>', '2021-09-20')
                            ->sum('value');
    }

    public function getBlockedBalance() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('status_enum', Transaction::STATUS_TRANSFERRED)
                            ->whereHas('blockReasonSale',function ($query) {
                                    $query->where('status', BlockReasonSale::STATUS_BLOCKED);
                            })
                            ->sum('value');
    }

    public function getBlockedBalancePending() : int
    {
        return Transaction::where('company_id', $this->company->id)
                            ->whereIn('gateway_id', $this->gatewayIds)
                            ->where('status_enum', Transaction::STATUS_PENDING)
                            ->whereHas('blockReasonSale',function ($query) {
                                    $query->where('status', BlockReasonSale::STATUS_BLOCKED);
                            })
                            ->sum('value');
    }

    public function getPendingDebtBalance() : int
    {
        return 0;
    }

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        return false;
    }

    public function getWithdrawals(): JsonResource
    {
        $withdrawals = Withdrawal::where('company_id', $this->company->id)
                                    ->whereIn('gateway_id', $this->gatewayIds)
                                    ->orderBy('id', 'DESC');

        return WithdrawalResource::collection($withdrawals->paginate(10));
    }

    public function withdrawalValueIsValid($value): bool
    {
        $availableBalance = $this->getAvailableBalance();

        if (empty($value) || $value < 1 || $value > $availableBalance) {
            return false;
        }

        return true;
    }

    public function createWithdrawal($value): bool
    {
        try {
            DB::beginTransaction();

            $this->company->update([
                'asaas_balance' => $this->company->asaas_balance -= $value
            ]);

            $withdrawal = Withdrawal::where([
                                        ['company_id', $this->company->id],
                                        ['status', Withdrawal::STATUS_PENDING],
                                ])
                                ->whereIn('gateway_id', $this->gatewayIds)
                                ->first();

            if (empty($withdrawal)) {

                $isFirstUserWithdrawal = (new WithdrawalService)->isFirstUserWithdrawal(auth()->user());

                $withdrawal = Withdrawal::create(
                    [
                        'value' => $value,
                        'company_id' => $this->company->id,
                        'bank' => $this->company->bank,
                        'agency' => $this->company->agency,
                        'agency_digit' => $this->company->agency_digit,
                        'account' => $this->company->account,
                        'account_digit' => $this->company->account_digit,
                        'status' => $isFirstUserWithdrawal ? Withdrawal::STATUS_IN_REVIEW : Withdrawal::STATUS_PENDING,
                        'tax' => 0,
                        'observation' => $isFirstUserWithdrawal ? 'Primeiro saque' : null,
                        'gateway_id' => foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID
                    ]
                );
            } else {
                $withdrawalValueSum = $withdrawal->value + $value;

                $withdrawal->update([
                    'value' => $withdrawalValueSum
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return false;
        }

        // event(new WithdrawalRequestEvent($withdrawal));

        return true;
    }

    public function updateAvailableBalance($saleId = null)
    {
        try {
            DB::beginTransaction();

            $transactions = Transaction::with('company')
                ->where('created_at', '>', '2021-09-15')
                ->where('release_date', '<=', Carbon::now()->format('Y-m-d'))
                ->where('status_enum', Transaction::STATUS_PAID)
                ->whereIn('gateway_id', $this->gatewayIds)
                ->whereNotNull('company_id')
                ->where(function ($where) {
                    $where->where('tracking_required', false)
                        ->orWhereHas('sale', function ($query) {
                            $query->where(function ($q) {
                                $q->where('has_valid_tracking', true)
                                    ->orWhereNull('delivery_id');
                            });
                        });
                });

            if (!empty($saleId)) {
                $transactions->where('sale_id', $saleId);
            }

            // dd($transactions->count());

            foreach ($transactions->cursor() as $transaction) {
                $company = $transaction->company;

                Transfer::create(
                    [
                        'transaction_id' => $transaction->id,
                        'user_id' => $company->user_id,
                        'company_id' => $company->id,
                        'type_enum' => Transfer::TYPE_IN,
                        'value' => $transaction->value,
                        'type' => 'in',
                        'gateway_id' => foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID
                    ]
                );

                $company->update([
                    'asaas_balance' => $company->asaas_balance + $transaction->value
                ]);

                $transaction->update([
                    'status' => 'transfered',
                    'status_enum' => Transaction::STATUS_TRANSFERRED,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
        }
    }

    public function getStatement($filters)
    {
        return (new StatementService)->getDefaultStatement($this->company->id, $this->gatewayIds, $filters);
    }

    public function getResume()
    {
        $lastTransaction = Transaction::whereIn('gateway_id', $this->gatewayIds)->orderBy('id', 'desc')->first();

        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $blockedBalance = $this->getBlockedBalance();
        $totalBalance = $availableBalance + $pendingBalance - $blockedBalance;
        $lastTransactionDate = !empty($lastTransaction) ? $lastTransaction->created_at->format('d/m/Y') : '';

        return [
            'name' => 'Asaas',
            'available_balance' => foxutils()->formatMoney($availableBalance / 100),
            'pending_balance' => foxutils()->formatMoney($pendingBalance / 100),
            'blocked_balance' => foxutils()->formatMoney($blockedBalance / 100),
            'total_balance' => foxutils()->formatMoney($totalBalance / 100),
            'last_transaction' => $lastTransactionDate,
            'id' => 'NzJqoR32egVj5D6'
        ];
    }

    public function makeAnticipationSale(Sale $sale)
    {
        $apiKey = $this->getCompanyApiKey($sale->owner_id, $sale->project_id);

        $curl = curl_init('https://www.asaas.com/api/v3/anticipations');

        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'access_token: ' . $apiKey,
        ]);

        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            "agreementSignature"=> $sale->customer->name,
            "installment"=> null,
            "payment"=> $sale->gateway_transaction_id,
            "documents"=> null
        ]);

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result, true);
    }

    public function getCompanyApiKey($owner_id,$project_id)
    {
        return UserProject::where('user_id', $owner_id)
            ->where('project_id', $project_id)
            ->first()
            ->company
            ->getGatewayApiKey(foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID);

    }
}
