<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

/**
 * Class WithdrawalPresenter
 * @package Modules\Core\Presenters
 */
class WithdrawalPresenter extends Presenter
{
    /**
     * @param $status
     * @return int|string
     */
    public function getStatus($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'pending';
                case 2:
                    return 'approved';
                case 3:
                    return 'transfered';
                case 4:
                    return 'refused';
                case 5:
                    return 'in_review';
                case 6:
                    return 'processing';
                case 7:
                    return 'returned';
            }

            return '';
        } else {
            switch ($status) {
                case 'pending':
                    return 1;
                case 'approved':
                    return 2;
                case 'transfered':
                    return 3;
                case 'refused':
                    return 4;
                case 'in_review':
                    return 5;
                case 'processing':
                    return 6;
                case 'returned':
                    return 7;
            }

            return '';
        }
    }

    /**
     * @return string
     */
    public function getDateReleaseFormatted($releaseDate)
    {
        if (!empty($releaseDate)) {
            if (strstr($releaseDate->format('d/m/Y H:i:s'), '00:00:00')) {
                return $releaseDate->format('d/m/Y');
            } else {
                return $releaseDate->format('d/m/Y H:i:s');
            }
        } else {
            return '';
        }
    }
}
