<?php

namespace Modules\Core\Services\DemoAccount;


use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Cashback;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Gateway;
use Modules\Core\Entities\GatewaysCompaniesCredential;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Transaction;
use Modules\Core\Services\FoxUtils;

/**
 * Class SplitPayment
 * @package App\Services
 */
class DemoSplitPayment
{
    public $user;
    public $sale;
    public $cloudfoxValue;
    public $producerValue;
    public $transactionStatus;
    public $producerCompany;
    public $cashbackData;

    public static function perform(Sale $sale)
    {
        (new static())->handle($sale);
    }

    private function handle(Sale $sale)
    { 
        $this->sale = $sale;
        
        $this->checkOldTransactions()
            ->setTransactionsStatus()
            ->checkCashback()
            ->getProducerCompany()
            ->setCloudfoxValue()
            ->setProducerValue()
            ->checkAffiliate()
            ->checkInstallmentsFreeTax()
            ->checkPartners()
            ->checkProducerInvitation()
            ->createProducerTransaction()
            ->createCloudfoxTransaction();
    }

    private function checkOldTransactions()
    {
        if ($this->sale->transactions->count() > 0) {
            $this->sale->transactions()->delete();
        }
        return $this;
    }

    private function setTransactionsStatus()
    {
        $this->transactionStatus = '';

        if ($this->sale->status == Sale::STATUS_IN_REVIEW) {
            $this->transactionStatus = Transaction::STATUS_PENDING_ANTIFRAUD;
            return $this;
        }
        
        switch ($this->sale->payment_method) {
            case Sale::CREDIT_CARD_PAYMENT:
                switch ($this->sale->status) {
                    case Sale::STATUS_APPROVED:
                        $this->transactionStatus = Transaction::STATUS_PAID;
                    break;
                    case Sale::STATUS_CANCELED_ANTIFRAUD:
                        $this->transactionStatus = Transaction::STATUS_CANCELED_ANTIFRAUD;
                        break;
                    default:
                        $this->transactionStatus = Transaction::STATUS_IN_PROCESS;
                    break;
                }
                break;            
            case  Sale::DEBIT_PAYMENT:
                $this->transactionStatus = $this->sale->status == Sale::STATUS_APPROVED ? Transaction::STATUS_PAID : Transaction::STATUS_IN_PROCESS;
                break;
            case Sale::BILLET_PAYMENT:
                $this->transactionStatus = $this->sale->status == Sale::STATUS_APPROVED ? Transaction::STATUS_PAID : Transaction::STATUS_PENDING;
                break;
            case Sale::PIX_PAYMENT:
                $this->transactionStatus = $this->sale->status == Sale::STATUS_APPROVED ? Transaction::STATUS_PAID : Transaction::STATUS_PENDING;
                break;
        }    

        return $this;
    }

    private function checkCashback()
    { 
        $this->cashbackData = [
            'value' => $this->getCashbackValue(),
            'percentage' => $this->getPercentage(),
        ];
        
        return $this;
    }

    public function getCashbackValue(): int
    {   
        try {
            if ($this->sale->payment_method == Sale::BILLET_PAYMENT || $this->sale->installments_amount == 1) {
                return 0;
            }

            if ($this->sale->payment_method == Sale::PIX_PAYMENT || $this->sale->installments_amount == 1) {
                return 0;
            }

            $user = $this->sale->user;

            if (empty($user)) {
                report(new Exception('Usuário não encontrado no calculo do cashback'));
                return 0;
            }

            $installmentsAmount = $this->sale->installments_amount - 1;

            $cashbackPercentage = $installmentsAmount * $user->installment_cashback;
            
            $saleValueWithoutTax = (FoxUtils::onlyNumbers($this->sale->total_paid_value) - FoxUtils::onlyNumbers($this->sale->interest_total_value));

            $cashbackValue = (int)($saleValueWithoutTax / 100 * $cashbackPercentage);
                       
            return $cashbackValue;
            
        } catch (Exception $e) {
            report($e);
            return 0;
        }
    }

    public function getPercentage()
    {
        try {
            return ($this->sale->installments_amount - 1) * $this->sale->user->installment_cashback;
        } catch (Exception $e) {
            report($e);
            return 0;
        }
    }

    private function setCloudfoxValue()
    { 
        if ($this->sale->payment_method == Sale::CREDIT_CARD_PAYMENT)
        {
            $total = $this->sale->original_total_paid_value - $this->sale->interest_total_value + $this->cashbackData['value'];
            
            $this->cloudfoxValue = (int)(($total/100) * $this->producerCompany->gateway_tax);

            $this->cloudfoxValue += FoxUtils::onlyNumbers($this->producerCompany->transaction_rate);
            
            $this->cloudfoxValue += $this->sale->interest_total_value;            
        } else {
            $this->cloudfoxValue = (int)(($this->sale->original_total_paid_value / 100) * $this->producerCompany->gateway_tax);

            if (FoxUtils::onlyNumbers($this->sale->total_paid_value) < 4000 && $this->sale->payment_method == Sale::BILLET_PAYMENT) {
                $transactionRate = 300;
            } else {
                $transactionRate = $this->producerCompany->transaction_rate;
            }

            $this->cloudfoxValue += FoxUtils::onlyNumbers($transactionRate);
        }
        
        return $this;
    }

    private function setProducerValue()
    { 
        $this->producerValue = (int)$this->sale->original_total_paid_value - $this->cloudfoxValue;
        
        return $this;
    }

    private function checkAffiliate()
    { 
        if (!empty($this->sale->affiliate_id) || $this->sale->api_flag) {
            $affiliate = $this->sale->affiliate;

            if (!empty($affiliate) && $affiliate->status_enum == Affiliate::STATUS_ACTIVE ) 
            {
                $affiliateValue = intval((($this->sale->original_total_paid_value - $this->cloudfoxValue) / 100) * $affiliate->percentage);
                $this->producerValue -= $affiliateValue;

                $transactionData = $this->getTransactionData($affiliate->company);
                
                Transaction::create([
                    'sale_id' => $this->sale->id,
                    'gateway_id' => $this->sale->gateway_id,
                    'company_id' => $affiliate->company->id,
                    'user_id' => $affiliate->company->user_id,
                    'value' => $affiliateValue,
                    'transaction_rate' => $transactionData['transaction_rate'],
                    'status_enum' =>  $this->transactionStatus,
                    'status' => (new Transaction())->present()->getStatusEnum($this->transactionStatus),
                    'type' => Transaction::TYPE_AFFILIATE,
                    'release_date' => $this->getReleaseDate($affiliate->company),
                    'created_at'=>$this->sale->created_at,
                    'updated_at'=>$this->sale->updated_at
                ]);
            }
        }

        return $this;
    }

    private function checkInstallmentsFreeTax()
    {  
        if ($this->sale->payment_method == Sale::CREDIT_CARD_PAYMENT) {
            $this->producerValue -= $this->sale->installment_tax_value;
            $this->cloudfoxValue += $this->sale->installment_tax_value;
        }
        
        return $this;
    }

    private function checkPartners()
    { 
        return $this;
    }

    private function checkProducerInvitation()
    {
        $invite = Invitation::with('company')
            ->where([
                ['user_invited', $this->sale->owner_id],
                ['status', Invitation::STATUS_ACTIVE],
            ])->first();

        if (!empty($invite) && !empty($invite->company_id)) {
            if (in_array($this->sale->gateway_id, [Sale::GETNET_PRODUCTION_ID,Sale::GETNET_SANDBOX_ID])
                && $invite->company->getGatewayStatus(Gateway::GETNET_PRODUCTION_ID) != GatewaysCompaniesCredential::GATEWAY_STATUS_APPROVED
            ) {
                return $this;
            }

            $inviteValue = intval(($this->producerValue / 100 * 1));

            Transaction::create([
                'sale_id' => $this->sale->id,
                'gateway_id' => $this->sale->gateway_id,
                'company_id' => $invite->company_id,
                'user_id' => $invite->company->user_id,
                'value' => $inviteValue,
                'status_enum' => $this->transactionStatus,
                'status' => (new Transaction())->present()->getStatusEnum($this->transactionStatus),
                'invitation_id' => $invite->id,
                'type' => Transaction::TYPE_INVITATION,
                'release_date' => $this->getReleaseDate($invite->company),
                'tracking_required' => $this->sale->user->get_faster ? false : true,
                'created_at'=>$this->sale->created_at,
                'updated_at'=>$this->sale->updated_at
            ]);

            $this->cloudfoxValue -= $inviteValue;            
        }

        return $this;
    }

    private function createProducerTransaction()
    {         
        if ($this->cashbackData['value']) {
            $this->producerValue += $this->cashbackData['value'];
            $this->cloudfoxValue -= $this->cashbackData['value'];
        }

        $transactionData = $this->getTransactionData($this->producerCompany);

        $transaction = Transaction::create([
            'sale_id' => $this->sale->id,
            'gateway_id' => $this->sale->gateway_id,
            'company_id' => $this->producerCompany->id,
            'user_id' => $this->producerCompany->user_id,
            'value' => $this->producerValue,
            'status_enum' => $this->transactionStatus,
            'status' => (new Transaction())->present()->getStatusEnum($this->transactionStatus),
            'percentage_rate' => $transactionData['percentage_tax'],
            'transaction_rate' => $transactionData['transaction_rate'],
            'type' => Transaction::TYPE_PRODUCER,
            'installment_tax' => $this->producerCompany->installment_tax,
            'release_date' => $this->getReleaseDate($this->producerCompany),
            'tracking_required' => $this->sale->user->get_faster ? false : true,
            'is_security_reserve' => $this->isSecurityReserve($this->producerCompany),
            'created_at'=>$this->sale->created_at,
            'updated_at'=>$this->sale->updated_at
        ]);

        if ($this->cashbackData['value']) {
            Cashback::create([
                'user_id' => $this->producerCompany->user_id,
                'company_id' => $this->producerCompany->id,
                'transaction_id' => $transaction->id,
                'sale_id' => $this->sale->id,
                'value' => $this->cashbackData['value'],
                'percentage' => $this->cashbackData['percentage'],
                'created_at'=>$this->sale->created_at,
                'updated_at'=>$this->sale->updated_at
            ]);
        }

        return $this;
    }

    private function createCloudfoxTransaction()
    { 
        Transaction::create([
            'sale_id' => $this->sale->id,
            'gateway_id' => $this->sale->gateway_id,
            'value' => $this->cloudfoxValue,
            'status_enum' => $this->transactionStatus,
            'status' => (new Transaction())->present()->getStatusEnum($this->transactionStatus),
            'type' => Transaction::TYPE_CLOUDFOX,
            'created_at'=>$this->sale->created_at,
            'updated_at'=>$this->sale->updated_at
        ]);

        return $this;
    }

    private function getTransactionData(Company $company)
    { 
        if (FoxUtils::onlyNumbers($this->sale->total_paid_value) <= 4000 && $this->sale->payment_method == Sale::BILLET_PAYMENT) {
            $transactionRate = 300;
        } else {
            $transactionRate = $company->transaction_rate;
        }

        return [
            'percentage_tax' => $company->gateway_tax ?? null,
            'transaction_rate' => $transactionRate ?? null
        ];
    }

    private function getProducerCompany()
    { 
        if (!request()->api_flag) {
            $this->producerCompany = $this->sale->project->checkoutConfig->company;
        } else {
            $company = Company::find(request()->company_id);

            $this->producerCompany = $company;
        }

        return $this;
    }

    public function isSecurityReserve(Company $company)
    {
        if(($company->user->release_count == 0) and ($company->user->has_security_reserve)) {
            return true;
        }
        return false;
    }

    private function getReleaseDate(Company $company)
    {
        if(in_array($this->sale->payment_method,  [Sale::CREDIT_CARD_PAYMENT, Sale::PIX_PAYMENT]) && !in_array($this->sale->gateway_id, [Gateway::GETNET_PRODUCTION_ID, Gateway::GETNET_SANDBOX_ID])) {

            $today = Carbon::parse($this->sale->start_date);

            if ($company->user->has_security_reserve) {
                $releaseCount = $company->user->release_count + 1;

                if ($releaseCount == 20) {
                    $releaseDate = $today->addDays(90)->format('Y-m-d');
                    $releaseCount = 0;
                }

                $company->user->update(
                    [
                        'release_count' => $releaseCount
                    ]
                );

                if($releaseCount == 0) {
                    return $releaseDate;
                }
            }

            if(empty($this->sale->delivery_id) && $company->gateway_release_money_days < 7) {
                $releaseDate = $today->addDays(7)->format('Y-m-d');
            }
            else {
                $releaseDate = $today->addDays($company->gateway_release_money_days)->format('Y-m-d');
            }

            if (Carbon::parse($releaseDate)->isWeekend()) {
                $releaseDate = Carbon::parse($releaseDate)->nextWeekday()->format('Y-m-d');
            }
            return $releaseDate;

        }
        return null;
    }

}
