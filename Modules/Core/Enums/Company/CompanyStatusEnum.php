<?php
declare(strict_types=1);

namespace Modules\Core\Enums\Company;

enum CompanyStatusEnum: int
{
    case ANALYZING = 2;
    case APPROVED = 3;
    case REFUSED = 4;
}
