<?php

declare(strict_types=1);

namespace Modules\Core\Enums\Payments;

enum CardFlagEnum: int
{
    case CARD = 1;
    case VISA = 2;
    case ELO = 3;

    case AMEX = 4;
    case MASTER_CARD = 5;
    case HYPER_CARD = 6;
    case DISCOVER = 7;
}
