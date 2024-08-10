<?php
declare(strict_types=1);

namespace Modules\Core\Enums\Company;

enum CompanyDocumentTypeEnum: int
{
    case BANK = 1;
    case ADDRESS = 2;
    case CONTRACT = 3;

    public function label(): string
    {
        return match ($this) {
            self::BANK => 'bank_document_status',
            self::ADDRESS => 'address_document_status',
            self::CONTRACT => 'contract_document_status',
        };
    }
}
