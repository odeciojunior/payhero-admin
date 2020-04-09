<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class CustomerBankAccountPresenter extends Presenter
{
    public function getAccountType($accountType = null)
    {
        $accountType = $accountType ?? $this->account_type;

        if (is_numeric($accountType)) {
            switch ($accountType) {
                case 1:
                    return 'Conta corrente';
                case 2:
                    return 'Conta poupança';
                default:
                    return '';
            }
        } else {
            switch ($accountType) {
                case 'Conta corrente':
                    return 1;
                case 'Conta poupança':
                    return 2;
                default:
                    return '';
            }
        }
    }
}

