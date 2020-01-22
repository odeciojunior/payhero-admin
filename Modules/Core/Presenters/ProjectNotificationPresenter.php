<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;

class ProjectNotificationPresenter extends Presenter
{
    public function getEventEnum($event)
    {

        if (is_numeric($event)) {
            switch ($event) {
                case 1:
                    return 'billet_generated';
                case 2:
                    return 'billet_paid';
                case 3:
                    return 'credit_paid';
                case 4:
                    return 'abandoned_cart';
                case 5:
                    return 'billet_winning';
                case 6:
                    return 'tracking';
            }

            return '';
        } else {
            switch ($event) {
                case 'billet_generated':
                    return 1;
                case 'billet_paid':
                    return 2;
                case 'credit_paid':
                    return 3;
                case 'abandoned_cart':
                    return 4;
                case 'billet_winning':
                    return 5;
                case 'tracking':
                    return 6;
            }

            return '';
        }
    }

    public function getTypeEnum($event)
    {

        if (is_numeric($event)) {
            switch ($event) {
                case 1:
                    return 'email';
                case 2:
                    return 'sms';
            }

            return '';
        } else {
            switch ($event) {
                case 'email':
                    return 1;
                case 'sms':
                    return 2;
            }

            return '';
        }
    }

    public function getNotificationEnum($enum)
    {
        if (is_numeric($enum)) {
            switch ($enum) {
                case 1:
                    return 'sms_billet_generated_';
                case 2:
                    return 'billet_paid';
                case 3:
                    return 'credit_paid';
                case 4:
                    return 'abandoned_cart';
                case 5:
                    return 'billet_due_today';
                case 6:
                    return 'tracking';
                case 7:
                    return 'tracking';
                case 8:
                    return 'tracking';
                case 9:
                    return 'tracking';
                case 10:
                    return 'tracking';
                case 11:
                    return 'tracking';
                case 12:
                    return 'tracking';
                case 13:
                    return 'tracking';
                case 14:
                    return 'tracking';
                case 15:
                    return 'tracking';
            }

            return '';
        } else {
            switch ($enum) {
                case 'billet_generated':
                    return 1;
                case 'billet_paid':
                    return 2;
                case 'credit_paid':
                    return 3;
                case 'abandoned_cart':
                    return 4;
                case 'billet_winning':
                    return 5;
                case 'tracking':
                    return 6;
                case 'tracking':
                    return 7;
                case 'tracking':
                    return 8;
                case 'tracking':
                    return 9;
                case 'tracking':
                    return 10;
                case 'tracking':
                    return 11;
                case 'tracking':
                    return 12;
                case 'tracking':
                    return 13;
                case 'tracking':
                    return 14;
                case 'tracking':
                    return 15;
            }

            return '';
        }
    }

    public function getStatus($status)
    {

        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return 'active';
                case 2:
                    return 'disabled';
            }

            return '';
        } else {
            switch ($status) {
                case 'active':
                    return 1;
                case 'disabled':
                    return 2;
            }

            return '';
        }
    }
}
