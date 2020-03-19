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
// use App\Events\SplitPaymentReadyEvent;

class SplitPayment
{
    public $user;
    public $sale;
    public $totalValue;
    public $cloudfoxValue;
    public $producerValue;
    public $transactionStatus;
    public $installmentFreeTax;

    /**
     * Use cases pattern -> https://laracasts.com/series/whip-monstrous-code-into-shape/episodes/2
     */
    public static function perform(Sale $sale, $totalValue, $cloudfoxValue, $installmentFreeTax)
    {

        return (new static)->handle($sale, $totalValue, $cloudfoxValue, $installmentFreeTax);
    }

    /**
     * @param Sale $sale
     * @param $totalValue
     * @param $cloudfoxValue
     * @param $installmentFreeTax
     */
    private function handle(Sale $sale, $totalValue, $cloudfoxValue, $installmentFreeTax)
    {

        $this->sale               = $sale;
        $this->totalValue         = $totalValue;
        $this->cloudfoxValue      = $cloudfoxValue;
        $this->installmentFreeTax = $installmentFreeTax;

        $this->setTransactionsStatus()
             ->setProducerValue()
             ->checkAffiliate()
             ->checkInstallmentsFreeTax()
             ->checkConvertaxIntegration()
             ->checkPartners()
             ->checkProducerInvitation()
             ->createProducerTransaction()
             ->createCloudfoxTransaction();
             // ->triggerEvent();
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

                $invite = Invitation::where([
                                                ['user_invited', $affiliate->user_id],
                                                ['status', (new Invitation)->present()->getStatus('active')],
                                            ])->first();

                if (!empty($invite) && !empty($invite->company_id)) {

                    $inviteValue = intval(($affiliateValue / 100 * 1));

                    Transaction::create([
                                            'sale_id'       => $this->sale->id,
                                            'company_id'    => $invite->company_id,
                                            'value'         => $inviteValue,
                                            'release_date'  => Carbon::now()->addDays(30)->format('Y-m-d'),
                                            'status'        => $this->transactionStatus,
                                            'status_enum'   => (new Transaction)->present()
                                                                                ->getStatusEnum($this->transactionStatus),
                                            'type'          => (new Transaction)->present()->getType('invitation'),
                                            'invitation_id' => $invite->id,
                                        ]);

                    $this->cloudfoxValue -= $inviteValue;
                }

                $releaseDate = null;
                if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('credit_card')) {
                    $percentageRate   = $this->sale->user->credit_card_tax;
                    $releaseMoneyDays = $this->sale->user->credit_card_release_money_days;
                    $releaseDate      = Carbon::now()->addDays($releaseMoneyDays)->format('Y-m-d');
                } else if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('boleto')) {
                    $percentageRate = $this->sale->user->boleto_tax;
                } else if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('debito')) {
                    $percentageRate   = $this->sale->user->debit_card_tax;
                    $releaseMoneyDays = $this->sale->user->debit_card_release_money_days;
                    $releaseDate      = Carbon::now()->addDays($releaseMoneyDays)->format('Y-m-d');
                }

                if (preg_replace("/[^0-9]/", "", $this->sale->total_paid_value) <= 4000 && $this->sale->payment_method == (new Sale)->present()
                                                                                                      ->getPaymentType('boleto')) {
                    $transactionRate = 300;
                } else {
                    $transactionRate = $this->sale->user->transaction_rate;
                }

                Transaction::create([
                                        'sale_id'          => $this->sale->id,
                                        'company_id'       => $affiliate->company->id,
                                        'value'            => $affiliateValue,
                                        'percentage_rate'  => $percentageRate,
                                        'release_date'     => $releaseDate,
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

    private function checkConvertaxIntegration()
    {

        $convertaxIntegration = ConvertaxIntegration::where('project_id', $this->sale->project->id)->first();

        if (!empty($convertaxIntegration)) {

            $convertaxUser    = User::find(27);
            $convertaxCompany = Company::find(29);

            $releaseDate = null;
            if ($this->sale->payment_method == 1) {
                $percentageRate            = $convertaxUser->credit_card_tax;
                $convertaXReleaseMoneyDays = $convertaxUser->credit_card_release_money_days;
                $releaseDate               = Carbon::now()->addDays($convertaXReleaseMoneyDays)->format('Y-m-d');
            } else if ($this->sale->payment_method == 2) {
                $percentageRate = $convertaxUser->boleto_tax;
            } else if ($this->sale->payment_method == 3) {
                $percentageRate            = $convertaxUser->debit_card_tax;
                $convertaXReleaseMoneyDays = $convertaxUser->debit_card_release_money_days;
                $releaseDate               = Carbon::now()->addDays($convertaXReleaseMoneyDays)->format('Y-m-d');
            }

            Transaction::create([
                                    'sale_id'          => $this->sale->id,
                                    'company_id'       => $convertaxCompany->id,
                                    'value'            => $convertaxIntegration->value,
                                    'release_date'     => $releaseDate,
                                    'status'           => $this->transactionStatus,
                                    'status_enum'      => (new Transaction)->present()
                                                                           ->getStatusEnum($this->transactionStatus),
                                    'currency'         => 'real',
                                    'percentage_rate'  => $percentageRate,
                                    'transaction_rate' => '1.00',
                                    'type'             => (new Transaction)->present()->getType('convertaX'),
                                ]);

            $this->producerValue -= $convertaxIntegration->value;
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
                                        ['status', (new Invitation)->present()->getStatus('active')],
                                    ])->first();

        if (!empty($invite) && !empty($invite->company_id)) {

            $releaseDate = null;
            if ($this->sale->payment_method == 1 || $this->sale->payment_method == 3) {
                $releaseDate = Carbon::now()->addDays(30)->format('Y-m-d');
            }

            $inviteValue = intval(($this->producerValue / 100 * 1));

            Transaction::create([
                                    'sale_id'       => $this->sale->id,
                                    'company_id'    => $invite->company->id,
                                    'value'         => $inviteValue,
                                    'release_date'  => $releaseDate,
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
            $transactionRate = $this->sale->user->transaction_rate;
        }

        $releaseDate = null;
        if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('credit_card')) {

            $percentageRate   = $this->sale->user->credit_card_tax;
            $releaseMoneyDays = $this->sale->user->credit_card_release_money_days;
            $releaseDate      = Carbon::now()->addDays($releaseMoneyDays)->format('Y-m-d');
        } else if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('boleto')) {

            $percentageRate = $this->sale->user->boleto_tax;
        } else if ($this->sale->payment_method == (new Sale)->present()->getPaymentType('debito')) {

            $percentageRate   = $this->sale->user->debit_card_tax;
            $releaseMoneyDays = $this->sale->user->debit_card_release_money_days;
            $releaseDate      = Carbon::now()->addDays($releaseMoneyDays)->format('Y-m-d');
        }

        Transaction::create([
                                'sale_id'          => $this->sale->id,
                                'company_id'       => $producerCompany->id,
                                'value'            => $this->producerValue,
                                'release_date'     => $releaseDate,
                                'status'           => $this->transactionStatus,
                                'status_enum'      => (new Transaction)->present()
                                                                       ->getStatusEnum($this->transactionStatus),
                                'percentage_rate'  => $percentageRate,
                                'transaction_rate' => $transactionRate,
                                'type'             => (new Transaction)->present()->getType('producer'),
                                'installment_tax'  => $this->sale->user->installment_tax,
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

    // private function triggerEvent()
    // {

    //     event(new SplitPaymentReadyEvent($this->sale, $this->producerValue));

    //     return $this;
    // }
}
