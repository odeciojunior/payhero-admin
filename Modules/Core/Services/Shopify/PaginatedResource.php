<?php

namespace Modules\Core\Services\Shopify;

use Psr\Http\Message\ResponseInterface;

class PaginatedResource
{
    private array $current;

    private $links = [];

    const LINK_REGEX = '/<(.*page_info=([a-z0-9\-_]+).*)>; rel="?{type}"?/i';

    public function __construct(ResponseInterface $response, string $resourceKey)
    {
        $this->links = $this->extractHeaderLink($response);
        $contents = json_decode($response->getBody()->getContents());
        $this->current = $contents->{$resourceKey};
    }

    public function current(): array
    {
        return $this->current;
    }

    private function extractHeaderLink(ResponseInterface $response)
    {
        if (!$response->hasHeader("Link")) {
            return [];
        }
        $links = [
            "next" => null,
            "previous" => null,
        ];
        foreach (array_keys($links) as $type) {
            $matched = preg_match(
                str_replace("{type}", $type, static::LINK_REGEX),
                $response->getHeader("Link")[0],
                $matches,
            );
            if ($matched) {
                $links[$type] = $matches[2];
            }
        }

        return $links;
    }

    public function hasNext(): bool
    {
        return !empty($this->links["next"]);
    }

    public function getNextPageInfo(): ?string
    {
        return $this->links["next"] ?? null;
    }

    public function hasPrev(): bool
    {
        return !empty($this->links["previous"]);
    }

    public function getPrevPageInfo(): ?string
    {
        return $this->links["previous"] ?? null;
    }
}
