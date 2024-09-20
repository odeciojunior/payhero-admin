<?php
declare(strict_types=1);

namespace Unit\Modules\Core\Services\Shopify;

use Exception;
use Modules\Core\Services\Shopify\Client;
use Modules\Core\Services\Shopify\OrderService;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_find_order_when_shopify_return_status_forbidden_should_return_null(): void
    {
        $client = new Client('80a55b-84.myshopify.com', '123123');
        $service = new OrderService($client);

        $result = $service->find(1);

        $this->assertNull($result);
    }
}
