<?php

namespace Modules\Core\Services\Gateways;

use Carbon\Carbon;
use Exception;
use PDF;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Entities\AsaasAnticipationRequests;
use Modules\Core\Entities\BlockReasonSale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\Transfer;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\Withdrawal;
use Modules\Core\Entities\SaleGatewayRequest;
use Modules\Core\Entities\SaleLog;
use Modules\Core\Entities\SaleRefundHistory;
use Modules\Core\Interfaces\Statement;
use Modules\Core\Services\FoxUtils;
use Modules\Core\Services\SaleService;
use Modules\Core\Services\StatementService;
use Modules\Withdrawals\Services\WithdrawalService;
use Modules\Withdrawals\Transformers\WithdrawalResource;

use function Clue\StreamFilter\fun;

class AsaasService implements Statement
{
    public Company $company;
    public $gatewayIds = [];
    public $apiKey;
    public $companyId;

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
        $availableBalance = $this->getAvailableBalance();
        $pendingBalance = $this->getPendingBalance();
        $blockedBalance = $this->getBlockedBalance();
        $availableBalance += $pendingBalance;
        $availableBalance -= $blockedBalance;

        $transaction = Transaction::where('sale_id', $sale->id)->where('user_id', auth()->user()->account_owner_id)->first();

        return $availableBalance >= $transaction->value;
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

    public function createWithdrawal($value)
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

                $isFirstUserWithdrawal = (new WithdrawalService)->isFirstUserWithdrawal($this->company->user_id);

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

        return $withdrawal;
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
        $lastTransaction = Transaction::whereIn('gateway_id', $this->gatewayIds)
                                        ->where('company_id', $this->company->id)
                                        ->orderBy('id', 'desc')->first();

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
            'total_available' => $availableBalance,
            'last_transaction' => $lastTransactionDate,
            'id' => 'NzJqoR32egVj5D6'
        ];
    }

    public function getGatewayAvailable(){
        $lastTransaction = DB::table('transactions')->whereIn('gateway_id', $this->gatewayIds)
                                        ->where('company_id', $this->company->id)
                                        ->orderBy('id', 'desc')->first();

        return !empty($lastTransaction) ? ['Asaas']:[];
    }

    public function makeAnticipation(Sale $sale, $saveRequests = true, $simulate = false) {
        $this->getCompanyApiKey($sale->owner_id, $sale->project_id);

        $data = [
            "agreementSignature"=> $sale->user->name,
        ];

        if($sale->installments_amount == 1) {
            $data["payment"] =$sale->gateway_transaction_id;
        } else {
            $saleInstallmentId = $this->saleInstallmentId($sale);
            $data["installment"] = $saleInstallmentId;
        }

        $url = 'https://www.asaas.com/api/v3/anticipations';
        if($simulate) $url = 'https://www.asaas.com/api/v3/anticipations/simulate';

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: multipart/form-data',
            'access_token: ' . $this->apiKey,
        ]);

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($curl);
        $httpStatus     = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($result, true);

        if(($httpStatus < 200 || $httpStatus > 299) && (!isset($response->errors))) {
            //report(new Exception('Erro na executação do Curl - Asaas Anticipations' . $url . ' - code:' . $httpStatus));
            report('Erro na executação do Curl - Asaas Anticipations' . $url . ' - code:' . $httpStatus . ' -- $sale->id = ' . $sale->id . ' -- ' . json_encode($response));
        }

        if($saveRequests) {
            $this->saveRequests($url, $response, $httpStatus, $data, $sale->id);
        }


        return $response;
    }

    public function checkAnticipation(Sale $sale)
    {
        $this->getCompanyApiKey($sale->owner_id, $sale->project_id);

        $curl = curl_init('https://www.asaas.com/api/v3/anticipations/' . $sale->anticipation_id);

        curl_setopt($curl, CURLOPT_ENCODING, '');
        //curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            'access_token: ' . $this->apiKey,
        ]);

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result, true);
    }

    public function getCompanyApiKey($owner_id,$project_id)
    {
        $company = UserProject::where('user_id', $owner_id)
            ->where('project_id', $project_id)
            ->first()
            ->company;

        $this->companyId = $company->id;
        $this->apiKey = $company->getGatewayApiKey(foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID);

    }

    private function saleInstallmentId(Sale $sale): ?string
    {
        $gatewayRequest = $sale->saleGatewayRequests()
            ->where('gateway_result->status', Gateway::PAYMENT_STATUS_CONFIRMED)
            ->latest()
            ->first();

        $result = json_decode($gatewayRequest->gateway_result, true);

        if (isset($result['id']) and $sale->gateway_transaction_id == $result['id'] and !empty($result['installment'])) {
            return $result['installment'];
        }

        if (empty($gatewayRequest)) {
            throw new Exception("Venda não tem o installment para antecipar !");
        }
        return null;
    }

    private function saveRequests($url, $result, $httpStatus, $data, $saleId)
    {
        AsaasAnticipationRequests::create(
            [
                'company_id' => $this->companyId,
                'sale_id' => $saleId,
                'sent_data' => json_encode(
                    [
                        'url' => $url,
                        'data' => $data
                    ]
                ),
                'response' => json_encode(
                    [
                        'result' => $result,
                        'status' => $httpStatus
                    ]
                )
            ]

        );
    }

    public function getGatewayId()
    {
        return FoxUtils::isProduction() ? Gateway::ASAAS_PRODUCTION_ID:Gateway::ASAAS_SANDBOX_ID;
    }

    public function cancel($sale, $response, $refundObservation): bool
    {
        try {
            DB::beginTransaction();
            $responseGateway = $response->response ?? [];
            $statusGateway = $response->status_gateway ?? '';

            SaleRefundHistory::create(
                [
                    'sale_id' => $sale->id,
                    'refunded_amount' => foxutils()->onlyNumbers($sale->total_paid_value),
                    'date_refunded' => Carbon::now(),
                    'gateway_response' => json_encode($responseGateway),
                    'refund_value' => foxutils()->onlyNumbers($sale->total_paid_value),
                    'refund_observation' => $refundObservation,
                    'user_id' => auth()->user()->account_owner_id,
                ]
            );

            $refundTransactions = $sale->transactions;
            
            $saleService = new SaleService();
            $saleTax = 0;
            if(!empty($sale->anticipation_status)){
                $cashbackValue = !empty($sale->cashback) ? $sale->cashback->value:0;
                $saleTax = $saleService->getSaleTaxRefund($sale,$cashbackValue);
            }

            foreach ($refundTransactions as $refundTransaction) {
                
                $company = $refundTransaction->company;
                if (!empty($company)) {

                    if ($refundTransaction->status_enum == Transaction::STATUS_TRANSFERRED) {

                        $refundValue = $refundTransaction->value;
                        if ($refundTransaction->type == Transaction::TYPE_PRODUCER) {
                            // if (!empty($refundTransaction->sale->automatic_discount)) {
                            //     $refundValue -= $refundTransaction->sale->automatic_discount;
                            // }
                            $refundValue += $saleTax;
                        }
                   
                        Transfer::create([
                            'transaction_id' => $refundTransaction->id,
                            'user_id' => $refundTransaction->user_id,
                            'company_id' => $refundTransaction->company_id,
                            'gateway_id' => $sale->gateway_id,
                            'value' => $refundValue,
                            'type' => 'out',
                            'type_enum' => Transfer::TYPE_OUT,
                            'reason' => 'refunded',
                            'is_refunded_tax' => 0
                        ]);
                   
                        $company->update([
                            'asaas_balance' => $company->asaas_balance -= $refundValue
                        ]);

                    } elseif(!empty($sale->anticipation_status))
                    {
                        if ($refundTransaction->type <> Transaction::TYPE_PRODUCER) continue;
                        
                        Transfer::create(
                            [
                                'transaction_id' => $refundTransaction->id,
                                'user_id' => $company->user_id,
                                'company_id' => $company->id,
                                'type_enum' => Transfer::TYPE_IN,
                                'value' => $refundTransaction->value,
                                'type' => 'in',
                                'gateway_id' => foxutils()->isProduction() ? Gateway::ASAAS_PRODUCTION_ID : Gateway::ASAAS_SANDBOX_ID
                            ]
                        );

                        $company->update([
                            'asaas_balance' => $company->asaas_balance += $refundTransaction->value
                        ]);

                        $refundValue = $refundTransaction->value;                        
                        // if (!empty($refundTransaction->sale->automatic_discount)) {
                        //     $refundValue -= $refundTransaction->sale->automatic_discount;
                        // }
                        $refundValue += $saleTax;                    
                   
                        Transfer::create([
                            'transaction_id' => $refundTransaction->id,
                            'user_id' => $refundTransaction->user_id,
                            'company_id' => $refundTransaction->company_id,
                            'gateway_id' => $sale->gateway_id,
                            'value' => $refundValue,
                            'type' => 'out',
                            'type_enum' => Transfer::TYPE_OUT,
                            'reason' => 'refunded',
                            'is_refunded_tax' => 0
                        ]);
                   
                        $company->update([
                            'asaas_balance' => $company->asaas_balance -= $refundValue
                        ]);
                    }

                }

                $refundTransaction->status = 'refunded';
                $refundTransaction->status_enum = Transaction::STATUS_REFUNDED;
                $refundTransaction->is_waiting_withdrawal = 0;
                $refundTransaction->save();
            }

            $sale->update(
                [
                    'status' => Sale::STATUS_REFUNDED,
                    'gateway_status' => $statusGateway,
                    'refund_value' => foxutils()->onlyNumbers($sale->total_paid_value),
                    'date_refunded' => Carbon::now(),
                ]
            );

            SaleLog::create(
                [
                    'sale_id' => $sale->id,
                    'status' => 'refunded',
                    'status_enum' => Sale::STATUS_REFUNDED,
                ]
            );

            DB::commit();

            return true;
        } catch (Exception $ex) {
            report($ex);
            DB::rollBack();
            throw $ex;
        }
    }

    public function refundReceipt($hashSaleId,$transaction)
    {
        $credential = DB::table('gateways_companies_credentials')->select('gateway_api_key')
        ->where('company_id',$transaction->company_id)->where('gateway_id',$transaction->gateway_id)->first();

        if(!empty($credential)){
            $this->apiKey = $credential->gateway_api_key;
        }

        $domainAsaas = 'https://www.asaas.com';
        $url = $domainAsaas.'/api/v3/payments/'.$transaction->sale->gateway_transaction_id;

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: multipart/form-data',
            'access_token: ' . $this->apiKey,
        ]);

        $result = curl_exec($curl);
        $httpStatus     = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $response = json_decode($result);

        if (($httpStatus < 200 || $httpStatus > 299) && (!isset($response->errors))) {
            //report(new Exception('Erro na executação do Curl - Asaas Anticipations' . $url . ' - code:' . $httpStatus));
            report('Erro ao consultar o status do pagamento' . $url . ' - code:' . $httpStatus);
        }

        if(!empty($response) && !empty($response->status) && $response->status=='REFUNDED' && !empty($response->transactionReceiptUrl)){

            $curl = curl_init($response->transactionReceiptUrl);

            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

            $result = curl_exec($curl);

            curl_close($curl);

            $of = [
                'href="/assets',
                'src="/assets',
                '</head>',
                'Cobrança intermediada por ASAAS - gerar boletos nunca foi tão fácil.'
            ];

            $to = [
                'href="' .$domainAsaas.'/assets',
                'src="'. $domainAsaas.'/assets',
                '<style>#loading-backdrop{display:none !important}</style>',
                ''
            ];

            $view = str_replace($of,$to,$result);

            return PDF::loadHtml($view);
        }

        return PDF::loadHtml('<h2>Não foi possivel gerar o comprovante de estorno!.</h2>');
    }
}
