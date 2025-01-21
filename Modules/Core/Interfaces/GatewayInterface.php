<?php

declare(strict_types=1);

namespace Modules\Core\Interfaces;

interface GatewayInterface
{
    public function createSubSellerAccount(array $data): array;
}
