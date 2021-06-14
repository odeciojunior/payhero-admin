<?php

namespace Modules\Core\Presenters;

use Modules\Core\Entities\Domain;
use Laracasts\Presenter\Presenter;
use Modules\Core\Services\FoxUtils;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property mixed sms_sent_amount
 * @property mixed email_sent_amount
 * @property mixed id_log_session
 */
class CheckoutPresenter extends Presenter
{
    /**
     * @return float|int
     */
    public function getSubTotal($checkoutPlans = null)
    {
        if (empty($checkoutPlans)) {
            $checkoutPlans = $this->checkoutPlans;
        }

        $total = 0;
        foreach ($checkoutPlans as $checkoutPlan) {
            $total += intval(preg_replace("/[^0-9]/", "", $checkoutPlan->plan->price)) * intval($checkoutPlan->amount);
        }

        return $total;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        $products = [];
        foreach ($this->checkoutPlans as $checkoutPlan) {
            foreach ($checkoutPlan->plan()->first()->productsPlans as $productPlan) {
                $product           = $productPlan->product()->first()->toArray();
                $product['amount'] = $productPlan->amount * $checkoutPlan->amount;
                $products[]        = $product;
            }
        }

        return $products;
    }

    public function getSmsSentAmount()
    {

        if ($this->sms_sent_amount == null || $this->sms_sent_amount == 0) {
            return 'Não enviado';
        } else {
            return $this->sms_sent_amount;
        }
    }

    public function getEmailSentAmount()
    {

        if ($this->email_sent_amount == null || $this->email_sent_amount == 0) {
            return 'Não enviado';
        } else {
            return $this->email_sent_amount;
        }
    }

    public function getCheckoutLink($domain)
    {

        $link = '';
        if(FoxUtils::isProduction()) {
            $link = isset($domain) ? 'https://checkout.' . $domain->name . '/recovery/' . Hashids::encode($this->id) : 'Domínio não configurado';
            //$link = isset($this->project->domains[0]->name) ? 'https://checkout.' . $this->project->domains[0]->name . '/' . $this->code : 'Domínio não configurado';
        } else {
            $link = env('CHECKOUT_URL', 'http://dev.checkout.com') . '/recovery/' . Hashids::encode($this->id);
        }

        return $link;
    }

    /**
     * @return float|int
     */
    public function getCheckoutIdIntegrations()
    {
        return 15 * $this->id;
    }

    public function getStatusEnum($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'accessed';
                case 2:
                    return 'abandoned cart';
                case 3:
                    return 'recovered';
                case 4:
                    return 'sale finalized';
            }
        } else {
            switch ($status) {
                case 'accessed':
                    return 1;
                case 'abandoned cart':
                    return 2;
                case 'recovered':
                    return 3;
                case 'sale finalized':
                    return 4;
            }
        }

        return '';
    }
}
