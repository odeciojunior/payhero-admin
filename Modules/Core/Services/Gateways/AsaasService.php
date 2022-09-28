<?php

namespace Modules\Core\Services\Gateways;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Task;
use Modules\Core\Services\TaskService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\AsaasAnticipationRequests;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\CompanyBankAccount;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\CompanyService;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\StatementService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

class AsaasService implements Statement
{
    public Company $company;
    public CompanyBankAccount $companyBankAccount;
    public $gatewayIds = [];
    public $apiKey;
    public $companyId;

    public function __construct()
    {
        $this->gatewayIds = [Gateway::ASAAS_PRODUCTION_ID, Gateway::ASAAS_SANDBOX_ID];
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    public function setBankAccount(CompanyBankAccount $companyBankAccount)
    {
        $this->companyBankAccount = $companyBankAccount;
    }

    public function getAvailableBalance(): int
    {
        return $this->company->asaas_balance;
    }

    public function getPendingBalance(): int
    {
        $cacheName = "balance-pending-asaas-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("transactions.company_id", $this->company->id)
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereIn("transactions.gateway_id", $this->gatewayIds)
                ->where("transactions.created_at", ">", "2021-09-20")
                ->sum("transactions.value");
        });
    }

    public function getPendingBalanceCount(): int
    {
        $cacheName = "balance-pending-count-asaas-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("transactions.company_id", $this->company->id)
                ->where("transactions.status_enum", Transaction::STATUS_PAID)
                ->whereIn("transactions.gateway_id", $this->gatewayIds)
                ->where("transactions.created_at", ">", "2021-09-20")
                ->count();
        });
    }

    public function getBlockedBalance(): int
    {
        $cacheName = "balance-blocked-asaas-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("company_id", $this->company->id)
                ->whereIn("gateway_id", $this->gatewayIds)
                ->whereIn("status_enum", [Transaction::STATUS_TRANSFERRED, Transaction::STATUS_PAID])
                ->join("block_reason_sales", "block_reason_sales.sale_id", "=", "transactions.sale_id")
                ->where("block_reason_sales.status", BlockReasonSale::STATUS_BLOCKED)
                ->sum("value");
        });
    }

    public function getBlockedBalanceCount(): int
    {
        $cacheName = "balance-blocked-count-asaas-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            return Transaction::where("company_id", $this->company->id)
                ->whereIn("gateway_id", $this->gatewayIds)
                ->join("block_reason_sales", "block_reason_sales.sale_id", "=", "transactions.sale_id")
                ->where("block_reason_sales.status", BlockReasonSale::STATUS_BLOCKED)
                ->count();
        });
    }

    public function getPendingDebtBalance(): int
    {
        return 0;
    }

    public function hasEnoughBalanceToRefund(Sale $sale): bool
    {
        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $availableBalance += $pendingBalance;

        $accountOwnerId = auth()->user()->account_owner_id ?? $sale->owner_id;
        $transaction = Transaction::where("sale_id", $sale->id)
            ->where("user_id", $accountOwnerId)
            ->first();

        return $availableBalance >= $transaction->value;
    }

    public function getWithdrawals(): JsonResource
    {
        $withdrawals = Withdrawal::where("company_id", $this->company->id)
            ->whereIn("gateway_id", $this->gatewayIds)
            ->orderBy("id", "DESC");

        return WithdrawalResource::collection($withdrawals->paginate(10));
    }

    public function withdrawalValueIsValid($value): bool
    {
        if (empty($value) || $value < 1) {
            return false;
        }

        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        (new CompanyService())->applyBlockedBalance($this, $availableBalance, $pendingBalance);

        if ($value > $availableBalance) {
            return false;
        }

        return true;
    }

    public function existsBankAccountApproved()
    {
        //verifica se existe uma chave pix aprovada
        $this->companyBankAccount = $this->company->getBankAccountTED();
        return !empty($this->companyBankAccount);
    }

    public function createWithdrawal($value)
    {
        try {
            DB::beginTransaction();

            $this->company->update([
                "asaas_balance" => ($this->company->asaas_balance -= $value),
            ]);

            $withdrawal = Withdrawal::where([
                ["company_id", $this->company->id],
                ["status", Withdrawal::STATUS_PENDING],
            ])
                ->whereIn("gateway_id", $this->gatewayIds)
                ->first();

            if (empty($withdrawal)) {
                $isFirstUserWithdrawal = (new WithdrawalService())->isFirstUserWithdrawal($this->company->user_id);

                if ($isFirstUserWithdrawal) {
                    TaskService::setCompletedTask($this->company->user, Task::find(Task::TASK_FIRST_WITHDRAWAL));
                }

                $withdrawal = Withdrawal::create([
                    "value" => $value,
                    "company_id" => $this->company->id,
                    "transfer_type" => "TED",
                    "bank" => $this->companyBankAccount->bank,
                    "agency" => $this->companyBankAccount->agency,
                    "agency_digit" => $this->companyBankAccount->agency_digit,
                    "account" => $this->companyBankAccount->account,
                    "account_digit" => $this->companyBankAccount->account_digit,
                    "status" => $isFirstUserWithdrawal ? Withdrawal::STATUS_IN_REVIEW : Withdrawal::STATUS_PENDING,
                    "tax" => 0,
                    "observation" => $isFirstUserWithdrawal ? "Primeiro saque" : null,
                    "gateway_id" => foxutils()->isProduction()
                        ? Gateway::ASAAS_PRODUCTION_ID
                        : Gateway::ASAAS_SANDBOX_ID,
                ]);
            } else {
                $withdrawalValueSum = $withdrawal->value + $value;

                $withdrawal->update([
                    "value" => $withdrawalValueSum,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);
            return false;
        }

        return $withdrawal;
    }

    public function updateAvailableBalance($saleId = null)
    {
        try {
            DB::beginTransaction();

            $transactions = Transaction::with("company")
                ->where("created_at", ">", "2021-09-15")
                ->where("release_date", "<=", Carbon::now()->format("Y-m-d"))
                ->where("status_enum", Transaction::STATUS_PAID)
                ->whereIn("gateway_id", $this->gatewayIds)
                ->whereNotNull("company_id")
                ->where(function ($where) {
                    $where->where("tracking_required", false)->orWhereHas("sale", function ($query) {
                        $query->where(function ($q) {
                            $q->where("has_valid_tracking", true)->orWhereNull("delivery_id");
                        });
                    });
                });

            if (!empty($saleId)) {
                $transactions->where("sale_id", $saleId);
            }

            foreach ($transactions->cursor() as $transaction) {
                $company = $transaction->company;

                Transfer::create([
                    "transaction_id" => $transaction->id,
                    "user_id" => $company->user_id,
                    "company_id" => $company->id,
                    "type_enum" => Transfer::TYPE_IN,
                    "value" => $transaction->value,
                    "type" => "in",
                    "gateway_id" => foxutils()->isProduction()
                        ? Gateway::ASAAS_PRODUCTION_ID
                        : Gateway::ASAAS_SANDBOX_ID,
                ]);

                $company->update([
                    "asaas_balance" => ($company->asaas_balance += $transaction->value),
                ]);

                $transaction->update([
                    "status" => "transfered",
                    "status_enum" => Transaction::STATUS_TRANSFERRED,
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
        return (new StatementService())->getDefaultStatement($this->company->id, $this->gatewayIds, $filters);
    }

    public function getPeriodBalance($filters)
    {
        return (new StatementService())->getPeriodBalance($this->company->id, $this->gatewayIds, $filters);
    }

    public function getResume()
    {
        $cacheName = "resume-asaas-{$this->company->id}";
        return cache()->remember($cacheName, 120, function () {
            $lastTransaction = Transaction::whereIn("gateway_id", $this->gatewayIds)
                ->where("company_id", $this->company->id)
                ->orderBy("id", "desc")
                ->first();

            if (empty($lastTransaction) && $this->company->asaas_balance == 0) {
                return [];
            }
            $lastTransactionDate = !empty($lastTransaction) ? $lastTransaction->created_at->format("d/m/Y") : "";

            $blockedBalance = $this->getBlockedBalance();
            $blockedBalanceCount = $this->getBlockedBalanceCount();
            $pendingBalance = $this->getPendingBalance();
            $pendingBalanceCount = $this->getPendingBalanceCount();
            $availableBalance = $this->getAvailableBalance();
            $totalBalance = $availableBalance + $pendingBalance;

            (new CompanyService())->applyBlockedBalance($this, $availableBalance, $pendingBalance, $blockedBalance);

            return [
                "name" => "Asaas",
                "available_balance" => $availableBalance,
                "pending_balance" => $pendingBalance,
                "pending_balance_count" => $pendingBalanceCount,
                "blocked_balance" => $blockedBalance,
                "blocked_balance_count" => $blockedBalanceCount,
                "total_balance" => $totalBalance,
                "total_available" => $availableBalance,
                "pending_debt_balance" => 0,
                "last_transaction" => $lastTransactionDate,
                "id" => "NzJqoR32egVj5D6",
            ];
        });
    }

    public function getGatewayAvailable()
    {
        $lastTransaction = DB::table("transactions")
            ->whereIn("gateway_id", $this->gatewayIds)
            ->where("company_id", $this->company->id)
            ->orderBy("id", "desc")
            ->first();

        return !empty($lastTransaction) ? ["Asaas"] : [];
    }

    public function makeAnticipation(Sale $sale, $saveRequests = true, $simulate = false)
    {
        $this->getCompanyApiKey($sale);

        $data = [
            "agreementSignature" => $sale->user->name,
        ];

        if ($sale->installments_amount == 1) {
            $data["payment"] = $sale->gateway_transaction_id;
        } else {
            $saleInstallmentId = $this->saleInstallmentId($sale);
            $data["installment"] = $saleInstallmentId;
        }

        $url = "https://www.asaas.com/api/v3/anticipations";
        if ($simulate) {
            $url = "https://www.asaas.com/api/v3/anticipations/simulate";
        }

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data", "access_token: " . $this->apiKey]);

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($result, true);

        if (($httpStatus < 200 || $httpStatus > 299) && !isset($response["errors"])) {
            //report('Erro na executação do Curl - Asaas Anticipations' . $url . ' - code:' . $httpStatus . ' -- $sale->id = ' . $sale->id . ' -- ' . json_encode($response));
        }

        if ($saveRequests) {
            $this->saveRequests($url, $response, $httpStatus, $data, $sale->id);
        }

        return $response;
    }

    public function checkAnticipation(Sale $sale, $saveRequests = true)
    {
        $this->getCompanyApiKey($sale);

        $url = "https://www.asaas.com/api/v3/anticipations/" . $sale->anticipation_id;
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_ENCODING, "");
        //curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json", "access_token: " . $this->apiKey]);

        $result = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = json_decode($result, true);

        if ($saveRequests) {
            $this->saveRequests($url, $response, $httpStatus, [], $sale->id);
        }

        return json_decode($result, true);
    }

    public function getCompanyApiKey(Sale $sale)
    {
        $company = $sale
            ->transactions()
            ->where("type", Transaction::TYPE_PRODUCER)
            ->first()->company;

        $this->companyId = $company->id;
        $this->apiKey = $company->getGatewayApiKey(
            foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID
        );
    }

    private function saleInstallmentId(Sale $sale): ?string
    {
        $gatewayRequest = $sale
            ->saleGatewayRequests()
            ->where("gateway_result->status", Gateway::PAYMENT_STATUS_CONFIRMED)
            ->latest()
            ->first();

        $result = json_decode($gatewayRequest->gateway_result, true);

        if (
            isset($result["id"]) and
            $sale->gateway_transaction_id == $result["id"] and
            !empty($result["installment"])
        ) {
            return $result["installment"];
        }

        if (empty($gatewayRequest)) {
            throw new Exception("Venda não tem o installment para antecipar !");
        }
        return null;
    }

    private function saveRequests($url, $result, $httpStatus, $data, $saleId)
    {
        AsaasAnticipationRequests::create([
            "company_id" => $this->companyId,
            "sale_id" => $saleId,
            "sent_data" => json_encode([
                "url" => $url,
                "data" => $data,
            ]),
            "response" => json_encode([
                "result" => $result,
                "status" => $httpStatus,
            ]),
        ]);
    }

    public function getGatewayId(): int
    {
        return foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID;
    }

    public function cancel($sale, $response, $refundObservation): bool
    {
        try {
            DB::beginTransaction();
            $responseGateway = $response->response ?? [];
            $statusGateway = $response->status_gateway ?? "";

            SaleRefundHistory::create([
                "sale_id" => $sale->id,
                "refunded_amount" => foxutils()->onlyNumbers($sale->total_paid_value),
                "date_refunded" => Carbon::now(),
                "gateway_response" => json_encode($responseGateway),
                "refund_value" => foxutils()->onlyNumbers($sale->total_paid_value),
                "refund_observation" => $refundObservation,
                "user_id" => auth()->user()->account_owner_id ?? $sale->owner_id,
            ]);

            $refundTransactions = $sale->transactions;

            $saleService = new SaleService();
            $saleTax = 0;
            if ($sale->payment_method == Sale::CREDIT_CARD_PAYMENT) {
                $cashbackValue = $sale->cashback()->first()->value ?? 0;
                $saleTax = $saleService->getSaleTaxRefund($sale, $cashbackValue);
            }
            $totalSale = $saleService->getSaleTotalValue($sale);

            foreach ($refundTransactions as $refundTransaction) {
                $company = $refundTransaction->company;
                if (!empty($company)) {
                    if ($refundTransaction->status_enum == Transaction::STATUS_TRANSFERRED) {
                        $refundValue = $refundTransaction->value;
                        if ($refundTransaction->type == Transaction::TYPE_PRODUCER) {
                            $refundValue += $saleTax;
                        }

                        if ($refundValue > $totalSale) {
                            $refundValue = $totalSale;
                        }

                        Transfer::create([
                            "transaction_id" => $refundTransaction->id,
                            "user_id" => $refundTransaction->user_id,
                            "company_id" => $refundTransaction->company_id,
                            "gateway_id" => $sale->gateway_id,
                            "value" => $refundValue,
                            "type" => "out",
                            "type_enum" => Transfer::TYPE_OUT,
                            "reason" => "refunded",
                            "is_refunded_tax" => 0,
                        ]);

                        $company->update([
                            "asaas_balance" => ($company->asaas_balance -= $refundValue),
                        ]);
                    } elseif ($sale->payment_method == Sale::CREDIT_CARD_PAYMENT) {
                        if ($refundTransaction->type != Transaction::TYPE_PRODUCER) {
                            continue;
                        }

                        Transfer::create([
                            "transaction_id" => $refundTransaction->id,
                            "user_id" => $company->user_id,
                            "company_id" => $company->id,
                            "type_enum" => Transfer::TYPE_IN,
                            "value" => $refundTransaction->value,
                            "type" => "in",
                            "gateway_id" => foxutils()->isProduction()
                                ? Gateway::ASAAS_PRODUCTION_ID
                                : Gateway::ASAAS_SANDBOX_ID,
                        ]);

                        $company->update([
                            "asaas_balance" => ($company->asaas_balance += $refundTransaction->value),
                        ]);

                        $refundValue = $refundTransaction->value + $saleTax;

                        if ($refundValue > $totalSale) {
                            $refundValue = $totalSale;
                        }

                        Transfer::create([
                            "transaction_id" => $refundTransaction->id,
                            "user_id" => $refundTransaction->user_id,
                            "company_id" => $refundTransaction->company_id,
                            "gateway_id" => $sale->gateway_id,
                            "value" => $refundValue,
                            "type" => "out",
                            "type_enum" => Transfer::TYPE_OUT,
                            "reason" => "refunded",
                            "is_refunded_tax" => 0,
                        ]);

                        $company->update([
                            "asaas_balance" => ($company->asaas_balance -= $refundValue),
                        ]);
                    }
                }

                $refundTransaction->status = "refunded";
                $refundTransaction->status_enum = Transaction::STATUS_REFUNDED;
                $refundTransaction->is_waiting_withdrawal = 0;
                $refundTransaction->save();
            }

            $sale->update([
                "status" => Sale::STATUS_REFUNDED,
                "gateway_status" => $statusGateway,
                "refund_value" => foxutils()->onlyNumbers($sale->total_paid_value),
                "date_refunded" => Carbon::now(),
            ]);

            SaleService::createSaleLog($sale->id, "refunded");

            DB::commit();

            return true;
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            throw $ex;
        }
    }

    public function refundEnabled(): bool
    {
        return true;
    }

    public function canRefund(Sale $sale): bool
    {
        return false;
    }
}
