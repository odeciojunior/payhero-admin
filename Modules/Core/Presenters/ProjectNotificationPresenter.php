<?php

namespace Modules\Core\Presenters;

use Laracasts\Presenter\Presenter;
use Modules\Core\Services\ProjectNotificationService;

class ProjectNotificationPresenter extends Presenter
{
    public function getEventEnum($event)
    {
        if (is_numeric($event)) {
            switch ($event) {
                case ProjectNotificationService::BOLETO_GENERATED:
                    return "billet_generated";
                case ProjectNotificationService::BOLETO_COMPENSATED:
                    return "billet_paid";
                case ProjectNotificationService::CARD_PAYMENT:
                    return "credit_card_paid";
                case ProjectNotificationService::ABANDONED_CART:
                    return "abandoned_cart";
                case ProjectNotificationService::BOLETO_EXPIRED:
                    return "billet_due_today";
                case ProjectNotificationService::TRACKING_CODE:
                    return "tracking";
                case ProjectNotificationService::PIX_GENERATED:
                    return "pix_generated";
                case ProjectNotificationService::PIX_COMPENSATED:
                    return "pix_compensated";
                case ProjectNotificationService::PIX_EXPIRED:
                    return "pix_expired";
            }

            return "";
        } else {
            switch ($event) {
                case "billet_generated":
                    return ProjectNotificationService::BOLETO_GENERATED;
                case "billet_paid":
                    return ProjectNotificationService::BOLETO_COMPENSATED;
                case "credit_card_paid":
                    return ProjectNotificationService::CARD_PAYMENT;
                case "abandoned_cart":
                    return ProjectNotificationService::ABANDONED_CART;
                case "billet_due_today":
                    return ProjectNotificationService::BOLETO_EXPIRED;
                case "tracking":
                    return ProjectNotificationService::TRACKING_CODE;
                case "pix_generated":
                    return ProjectNotificationService::PIX_GENERATED;
                case "pix_compensated":
                    return ProjectNotificationService::PIX_COMPENSATED;
                case "pix_expired":
                    return ProjectNotificationService::PIX_EXPIRED;
            }

            return "";
        }
    }

    public function getTypeEnum($event)
    {
        if (is_numeric($event)) {
            switch ($event) {
                case 1:
                    return "email";
                case 2:
                    return "sms";
            }

            return "";
        } else {
            switch ($event) {
                case "email":
                    return 1;
                case "sms":
                    return 2;
            }

            return "";
        }
    }

    public function getNotificationEnum($enum)
    {
        if (is_numeric($enum)) {
            switch ($enum) {
                case 1:
                    return "sms_billet_generated_immediate";
                case 2:
                    return "sms_billet_due_today";
                case 3:
                    return "sms_abandoned_cart_an_hour_later";
                case 4:
                    return "sms_abandoned_cart_next_day";
                case 5:
                    return "email_billet_generated_immediate";
                case 6:
                    return "email_billet_generated_next_day";
                case 7:
                    return "email_billet_generated_two_days_later";
                case 8:
                    return "email_billet_due_today";
                case 9:
                    return "email_abandoned_cart_an_hour_later";
                case 10:
                    return "email_abandoned_cart_next_day";
                case 11:
                    return "sms_credit_card_paid_immediate";
                case 12:
                    return "email_credit_card_paid_immediate";
                case 13:
                    return "email_billet_paid_immediate";
                case 14:
                    return "email_tracking_immediate";
                case 15:
                    return "sms_tracking_immediate";
            }

            return "";
        } else {
            switch ($enum) {
                case "sms_billet_generated_immediate":
                    return 1;
                case "sms_billet_due_today":
                    return 2;
                case "sms_abandoned_cart_an_hour_later":
                    return 3;
                case "sms_abandoned_cart_next_day":
                    return 4;
                case "email_billet_generated_immediate":
                    return 5;
                case "email_billet_generated_next_day":
                    return 6;
                case "email_billet_generated_two_days_later":
                    return 7;
                case "email_billet_due_today":
                    return 8;
                case "email_abandoned_cart_an_hour_later":
                    return 9;
                case "email_abandoned_cart_next_day":
                    return 10;
                case "sms_credit_card_paid_immediate":
                    return 11;
                case "email_credit_card_paid_immediate":
                    return 12;
                case "email_billet_paid_immediate":
                    return 13;
                case "email_tracking_immediate":
                    return 14;
                case "sms_tracking_immediate":
                    return 15;
            }

            return "";
        }
    }

    public function getStatus($status)
    {
        if (is_numeric($status)) {
            switch ($status) {
                case 1:
                    return "active";
                case 2:
                    return "disabled";
            }

            return "";
        } else {
            switch ($status) {
                case "active":
                    return 1;
                case "disabled":
                    return 2;
            }

            return "";
        }
    }
}
