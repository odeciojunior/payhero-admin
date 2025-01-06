<?php

declare(strict_types=1);

namespace Modules\Core\ValueObjects;

use Modules\Core\Exceptions\InvalidUrlException;

class Url
{
    private const HTTP_PREFIX = 'http://';
    private const HTTPS_PREFIX = 'https://';

    /**
     * @throws InvalidUrlException
     */
    public function __construct(
        private string $url,
    ) {
        $this->ensureStartsWithHttps();
        $this->ensureIsValidUrl();
    }

    private function ensureStartsWithHttps(): void
    {
        if (!str_starts_with($this->url, self::HTTP_PREFIX) && !str_starts_with($this->url, self::HTTPS_PREFIX)) {
            $this->url = sprintf('https://%s', $this->url);
        }

        if (str_starts_with($this->url, self::HTTP_PREFIX)) {
            $this->url = preg_replace('/^http:\/\//', self::HTTPS_PREFIX, $this->url);
        }
    }

    /**
     * @throws InvalidUrlException
     */
    private function ensureIsValidUrl(): void
    {
        if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
            throw new InvalidUrlException(sprintf('Invalid URL: %s', $this->url));
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isSecure(): bool
    {
        return parse_url($this->url, PHP_URL_SCHEME) === self::HTTPS_PREFIX;
    }

    public function getDomain(): ?string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    public function __toString(): string
    {
        return $this->url;
    }
}
