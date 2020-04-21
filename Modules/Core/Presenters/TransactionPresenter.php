<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class TransactionPresenter extends Presenter
{
    public function getType($type)
    {

        if (is_numeric($type)) {
            switch ($type) {
                case 1:
                    return 'cloudfox';
                case 2:
                    return 'producer';
                case 3:
                    return 'invitation';
                case 4:
                    return 'affiliate';
                case 5:
                    return 'partner';
                case 6:
                    return 'convertaX';
                case 7:
                    return 'refunded';
            }
            return '';
        } else {
            switch ($type) {
                case 'cloudfox':
                    return 1;
                case 'producer':
                    return 2;
                case 'invitation':
                    return 3;
                case 'affiliate':
                    return 4;
                case 'partner':
                    return 5;
                case 'convertaX':
                    return 6;
                case 'refunded':
                    return 7;
            }
            return '';
        }
    }

    public function getStatusEnum($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'transfered';
                case 2:
                    return 'paid';
                case 3:
                    return 'pending';
                case 4:
                    return 'chargedback';
                case 5:
                    return 'canceled';
                case 6:
                    return 'refunded';
                case 7:
                    return 'refused';
                case 8:
                    return 'pending_antifraud';
                case 9:
                    return 'canceled_antifraud';
                case 10:
                    return 'anticipated';
                }
            return '';
        } else {
            switch ($status) {
                case 'transfered':
                    return 1;
                case 'paid':
                    return 2;
                case 'pending':
                    return 3;
                case 'chargedback':
                    return 4;
                case 'canceled':
                    return 5;
                case 'refunded':
                    return 6;
                case 'refused':
                    return 7;
                case 'pending_antifraud':
                    return 8;
                case 'canceled_antifraud':
                    return 9;
                case 'anticipated':
                    return 10;
                }
            return '';
        }
    }
}

