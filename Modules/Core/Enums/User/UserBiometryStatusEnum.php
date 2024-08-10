<?php

declare(strict_types=1);

namespace Modules\Core\Enums\User;

enum UserBiometryStatusEnum: int
{
    case PENDING = 1;
    case IN_PROCESS = 2;
    case APPROVED = 3;
    case REFUSED = 4;
    case MANUALLY_APPROVED = 5;

    public static function isApproved(int $status): bool
    {
        return in_array($status, [self::APPROVED->value, self::MANUALLY_APPROVED->value], true);
    }
}
