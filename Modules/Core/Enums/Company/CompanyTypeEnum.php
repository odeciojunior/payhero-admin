<?php
declare(strict_types=1);

namespace Modules\Core\Enums\Company;

enum CompanyTypeEnum: int
{
    case PHYSICAL_PERSON = 1;
    case JURIDICAL_PERSON = 2;
}
