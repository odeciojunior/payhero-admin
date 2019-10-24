<?php

namespace Modules\Core\Services;

use Exception;
use Log;
use Modules\Core\Entities\User;

/**
 * Class UserNotificationService
 * @package Modules\Core\Services
 */
class UserNotificationService
{
    /**
     * @var array
     */
    private $activeNotificationsConfigs = [
        // "new_affiliation",
        // "new_affiliation_request",
        // "approved_affiliation",
        // "withdrawal_approved", // NÃO ESTÁ SENDO USADO
        "boleto_compensated",
        "domain_approved",
        "released_balance",
        "notazz",
        "sale_approved",
        "shopify",
        "billet_generated",
        "credit_card_in_proccess",
    ];

    /**
     * @param $user
     * @param string $notification
     * @return bool
     */
    public function verifyUserNotification($user, string $notification)
    {
        try {
            if (!$user instanceof User || FoxUtils::isEmpty($notification) || !in_array($notification, $this->activeNotificationsConfigs)) {
                return true;
            }

            $user->loadMissing(["userNotification"]);
            $userNotification = $user->userNotification ?? null;
            if (FoxUtils::isEmpty($userNotification)) {
                return true;
            }

            $response = $userNotification->$notification ?? null;
            if (!is_null($response)) {
                return $response;
            }

            return true;
        } catch (Exception $ex) {
            Log::error(__METHOD__);
            report($ex);

            return true;
        }
    }
}