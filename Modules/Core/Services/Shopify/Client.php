<?php

namespace Modules\Core\Services\Shopify;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;

function delayMiddleware(): callable
{
    return function (callable $handler) {
        return function (RequestInterface $request, array $options) use ($handler) {
            usleep(1000);
            return $handler($request, $options);
        };
    };
}

class Client
{
    private GuzzleClient $client;

    public function __construct(string $shop, string $accessToken)
    {
        $stack = HandlerStack::create();
        $stack->push(delayMiddleware(), "delay");

        $this->client = new GuzzleClient([
            "base_uri" => "https://$shop/admin/api/2024-01/",
            "headers" => [
                "Content-Type" => "application/json",
                "X-Shopify-Access-Token" => $accessToken,
            ],
            "handler" => $stack,
        ]);
    }

    public function getClient(): GuzzleClient
    {
        return $this->client;
    }
}
