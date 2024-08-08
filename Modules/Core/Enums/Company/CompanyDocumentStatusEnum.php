<?php
declare(strict_types=1);

namespace Modules\Core\Enums\Company;

enum CompanyDocumentStatusEnum: int
{
    case PENDING = 1;
    case ANALYZING = 2;
    case APPROVED = 3;
    case REFUSED = 4;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::ANALYZING => 'analyzing',
            self::APPROVED => 'approved',
            self::REFUSED => 'refused',
        };
    }
}
