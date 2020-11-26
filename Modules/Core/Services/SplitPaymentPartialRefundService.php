<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Modules\Core\Entities\User;
use Modules\Core\Entities\Sale;
use Modules\Core\Entities\Company;
use Modules\Core\Entities\Affiliate;
use Modules\Core\Entities\Invitation;
use Modules\Core\Entities\Transaction;
use Modules\Core\Entities\UserProject;
use Modules\Core\Entities\ConvertaxIntegration;

class SplitPaymentPartialRefundService
{
    public $user;
    public $sale;
    public $totalValue;
    public $cloudfoxValue;
    public $producerValue;
    public $transactionStatus;
    public $installmentFreeTax;
    public $refundedTransactions;

    /**
     * Use cases pattern -> https://laracasts.com/series/whip-monstrous-code-into-shape/episodes/2
     */
    public static function perform(Sale $sale, $totalValue, $cloudfoxValue, $installmentFreeTax, $refundedTransactions)
    {

        return (new static)->handle($sale, $totalValue, $cloudfoxValue, $installmentFreeTax, $refundedTransactions);
    }

    /**
     * @param Sale $sale
     * @param $totalValue
     * @param $cloudfoxValue
     * @param $installmentFreeTax
     */
    private function handle(Sale $sale, $totalValue, $cloudfoxValue, $installmentFreeTax, $refundedTransactions)
    {

        $this->sale                 = $sale;
        $this->totalValue           = $totalValue;
        $this->cloudfoxValue        = $cloudfoxValue;
        $this->installmentFreeTax   = $installmentFreeTax;
        $this->refundedTransactions = $refundedTransactions;

        $this->setTransactionsStatus()
             ->setProducerValue()
             ->checkAffiliate()
             ->checkInstallmentsFreeTax()
             ->checkPartners()
             ->checkProducerInvitation()
             ->createProducerTransaction()
             ->createCloudfoxTransaction();
    }

    private function setTransactionsStatus()
    {

        $this->transactionStatus = '';

        if ($this->sale->status == (new Sale)->present()->getStatus('in_review')) {
            $this->transactionStatus = 'pending_antifraud';
        } else if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('credit_card')) {
            if (in_array($this->sale->status, [(new Sale)->present()->getStatus('approved'), (new Sale)->present()->getStatus('partial_refunded')])) {
                $this->transactionStatus = 'paid';
            } else {
                $this->transactionStatus = 'in_process';
            }
        } else if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('debito')) {
            if (in_array($this->sale->status, [(new Sale)->present()->getStatus('approved'), (new Sale)->present()->getStatus('partial_refunded')])) {
                $this->transactionStatus = 'paid';
            } else {
                $this->transactionStatus = 'in_process';
            }
        } else if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('boleto')) {
            $this->transactionStatus = 'pending';
        }

        return $this;
    }

    private function setProducerValue()
    {

        $this->producerValue = (int) $this->totalValue - $this->cloudfoxValue;

        return $this;
    }

    private function checkAffiliate()
    {

        if (!empty($this->sale->affiliate_id)) {

            $affiliate = $this->sale->affiliate;

            if (!empty($affiliate) && $affiliate->status_enum == (new Affiliate)->present()->getStatus('active')) {
                $affiliateValue      = intval((($this->totalValue - $this->cloudfoxValue) / 100) * $affiliate->percentage);
                $this->producerValue -= $affiliateValue;

                $invite = Invitation::with('company')->where([
                                                ['user_invited', $affiliate->user_id],
                                                ['status', (new Invitation)->present()->getStatus('accepted')],
                                            ])->first();

                if (!empty($invite) && !empty($invite->company_id)) {

                    $inviteValue = intval(($affiliateValue / 100 * 1));

                    $transactionsRefunded = $this->refundedTransactions;
                    $transactionRefundedAffiliateInvite = $transactionsRefunded->firstWhere('invitation_id' , $invite->id);

                    Transaction::create([
                                            'sale_id'       => $this->sale->id,
                                            'company_id'    => $invite->company_id,
                                            'value'         => $inviteValue,
                                            'release_date'  => $transactionRefundedAffiliateInvite->release_date,
                                            'status'        => $this->transactionStatus,
                                            'status_enum'   => (new Transaction)->present()
                                                                                ->getStatusEnum($this->transactionStatus),
                                            'type'          => (new Transaction)->present()->getType('invitation'),
                                            'invitation_id' => $invite->id,
                                        ]);

                    $this->cloudfoxValue -= $inviteValue;
                }

                if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('credit_card')) {
                    $percentageRate = $invite->company->gateway_tax;
                } else if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('boleto')) {
                    $percentageRate = $invite->company->gateway_tax;
                }

                if (preg_replace("/[^0-9]/", "", $this->sale->total_paid_value) <= 4000 && $this->sale->payment_method == (new Sale)->present()
                                                                                                      ->getPaymentType('boleto')) {
                    $transactionRate = 300;
                } else {
                    $transactionRate = $invite->company->transaction_rate;
                }

                $transactionsRefunded = $this->refundedTransactions;
                $transactionRefundedAffiliate = $transactionsRefunded->firstWhere('company_id',  $affiliate->company->id);

                Transaction::create([
                                        'sale_id'          => $this->sale->id,
                                        'company_id'       => $affiliate->company->id,
                                        'value'            => $affiliateValue,
                                        'percentage_rate'  => $percentageRate,
                                        'release_date'     => $transactionRefundedAffiliate->release_date,
                                        'status'           => $this->transactionStatus,
                                        'status_enum'      => (new Transaction)->present()
                                                                               ->getStatusEnum($this->transactionStatus),
                                        'transaction_rate' => $transactionRate,
                                        'type'             => (new Transaction)->present()->getType('affiliate'),
                                    ]);
            }
        }

        return $this;
    }

    private function checkInstallmentsFreeTax()
    {

        if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('credit_card') ||
            $this->sale->payment_method == (new Sale)->present()->getPaymentType('debito')) {

            $this->producerValue -= $this->installmentFreeTax;
            $this->cloudfoxValue += $this->installmentFreeTax;
        }

        return $this;
    }

    private function checkPartners()
    {

        // UsersProjects (partners) split ...
        return $this;
    }

    private function checkProducerInvitation()
    {

        $invite = Invitation::where([
                                        ['user_invited', $this->sale->user->id],
                                        ['status', (new Invitation)->present()->getStatus('accepted')],
                                    ])->first();

        if (!empty($invite) && !empty($invite->company_id)) {

            $inviteValue = intval(($this->producerValue / 100 * 1));

            $transactionsRefunded = $this->refundedTransactions;
            $transactionRefundedProducerInvite = $transactionsRefunded->firstWhere('invitation_id' , $invite->id);

            Transaction::create([
                                    'sale_id'       => $this->sale->id,
                                    'company_id'    => $invite->company->id,
                                    'value'         => $inviteValue,
                                    'release_date'  => $transactionRefundedProducerInvite->release_date,
                                    'status'        => $this->transactionStatus,
                                    'status_enum'   => (new Transaction)->present()
                                                                        ->getStatusEnum($this->transactionStatus),
                                    'invitation_id' => $invite->id,
                                    'type'          => (new Transaction)->present()->getType('invitation'),
                                ]);

            $this->cloudfoxValue -= $inviteValue;
        }

        return $this;
    }

    private function createProducerTransaction()
    {

        $userProject = UserProject::where([
                                              ['type_enum', (new UserProject)->present()->getTypeEnum('producer')],
                                              ['project_id', $this->sale->project->id],
                                          ])->first();

        $producerCompany = $userProject->company;

        if (preg_replace("/[^0-9]/", "", $this->sale->total_paid_value) <= 4000 && $this->sale->payment_method == (new Sale)->present()
                                                                                              ->getPaymentType('boleto')) {
            $transactionRate = 300;
        } else {
            $transactionRate = $producerCompany->transaction_rate;
        }

        $transactionsRefunded = $this->refundedTransactions;
        $transactionRefundedProducer = $transactionsRefunded->firstWhere('type', (new Transaction)->present()->getType('producer'));

        Transaction::create([
                                'sale_id'          => $this->sale->id,
                                'company_id'       => $producerCompany->id,
                                'value'            => $this->producerValue,
                                'release_date'     => $transactionRefundedProducer->release_date,
                                'status'           => $this->transactionStatus,
                                'status_enum'      => (new Transaction)->present()
                                                                       ->getStatusEnum($this->transactionStatus),
                                'percentage_rate'  => $producerCompany->gateway_tax,
                                'transaction_rate' => $transactionRate,
                                'type'             => (new Transaction)->present()->getType('producer'),
                                'installment_tax'  => $producerCompany->installment_tax,
                            ]);

        return $this;
    }

    private function createCloudfoxTransaction()
    {

        Transaction::create([
                                'sale_id'     => $this->sale->id,
                                'value'       => $this->cloudfoxValue,
                                'status'      => $this->transactionStatus,
                                'status_enum' => (new Transaction)->present()->getStatusEnum($this->transactionStatus),
                                'currency'    => 'real',
                                'type'        => (new Transaction)->present()->getType('cloudfox'),
                            ]);

        return $this;
    }

}
