<?php

declare(strict_types=1);

namespace App\Shared\infrastructure\Gateways;

use Modules\Core\Interfaces\GatewayInterface;
use Modules\Core\Services\Gateways\CheckoutGateway;

class MonetixGateway implements GatewayInterface
{
    private CheckoutGateway $checkoutGateway;

    public function __construct(int $gatewayId)
    {
        $this->checkoutGateway = new CheckoutGateway($gatewayId);
    }

    public function createSubSellerAccount(array $data): array
    {
        return $this->checkoutGateway->createAccount($data);
    }
}
