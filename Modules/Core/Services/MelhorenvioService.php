<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Modules\Core\Entities\MelhorenvioIntegration;

class MelhorenvioService
{
    private const BASE_URL = "https://www.melhorenvio.com.br";

    private const BASE_URL_SANDBOX = "https://sandbox.melhorenvio.com.br";

    private const SCOPES = [
        "cart-read", // Visualização dos itens do carrinho
        "cart-write", // Cadastro e edição dos itens do carrinho
        "orders-read", // Visualização das etiquetas
        "shipping-calculate", // Cotação de fretes
    ];

    private string $baseUrl;

    private string $clientId;

    private string $clientSecret;

    private ?string $accessToken;

    private ?string $refreshToken;

    private int $expiration = 0;

    private string $callbackUrl;

    private array $defaultHeaders;

    private ?MelhorenvioIntegration $integration;

    public function __construct(MelhorenvioIntegration $integration = null)
    {
        if ($integration) {
            $this->setIntegration($integration)
                ->setClientId(env("MELHORENVIO_CLIENT_ID"))
                ->setClientSecret(env("MELHORENVIO_CLIENT_SECRET"))
                ->setAccessToken($integration->access_token)
                ->setRefreshToken($integration->refresh_token);
        }
        $this->setBaseUrl()
            ->setDefaultHeaders()
            ->setCallbackUrl();
    }

    public function getIntegration(): MelhorenvioIntegration
    {
        return $this->integration;
    }

    public function setIntegration(MelhorenvioIntegration $integration): MelhorenvioService
    {
        $this->integration = $integration;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    private function setClientId(string $clientId): MelhorenvioService
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    private function setClientSecret(string $clientSecret): MelhorenvioService
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    private function setAccessToken(string $accessToken = null): MelhorenvioService
    {
        $this->accessToken = $accessToken;

        if ($this->getAccessToken()) {
            $payloadBase64 = explode(".", $this->accessToken)[1];
            $payloadJson = base64_decode($payloadBase64);
            $payload = json_decode($payloadJson);
            $this->setExpiration($payload->exp);
        }

        return $this;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    private function setRefreshToken(string $refreshToken = null): MelhorenvioService
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getExpiration(): int
    {
        return $this->expiration;
    }

    private function setExpiration(int $expiration): MelhorenvioService
    {
        $this->expiration = $expiration;
        return $this;
    }

    private function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    private function setBaseUrl(): MelhorenvioService
    {
        $this->baseUrl = env("MELHORENVIO_SANDBOX", false) === true ? self::BASE_URL_SANDBOX : self::BASE_URL;
        return $this;
    }

    private function getDefaultHeaders(): array
    {
        return $this->defaultHeaders;
    }

    private function setDefaultHeaders(): MelhorenvioService
    {
        $this->defaultHeaders = ["User-Agent: Azcend Admin (noreply@azcend.com.br)", "Accept: application/json"];

        if ($this->getExpiration() > 0 && $this->getExpiration() < time()) {
            $this->refreshToken();
        }

        $accessToken = $this->getAccessToken();
        if (!empty($accessToken)) {
            $this->defaultHeaders[] = "Content-Type: application/json";
            $this->defaultHeaders[] = "Authorization: Bearer " . $accessToken;
        }

        return $this;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    private function setCallbackUrl(): void
    {
        $this->callbackUrl = route("melhorenvio.finish");
    }

    public function getScopes(): string
    {
        return implode(" ", self::SCOPES);
    }

    public function getAuthorizationUrl()
    {
        $data = [
            "client_id" => $this->getClientId(),
            "redirect_uri" => $this->getCallbackUrl(),
            "response_type" => "code",
            "scope" => $this->getScopes(),
        ];

        $integration = $this->getIntegration();
        if ($integration) {
            $data["state"] = hashids_encode($integration->id);
        }

        return $this->getBaseUrl() . "/oauth/authorize?" . http_build_query($data);
    }

    private function doRequest(string $uri = "/", array $data = null, string $method = "GET", array $headers = [])
    {
        $curl = curl_init();

        $url = $this->getBaseUrl() . $uri;

        $method = strtoupper($method);

        if ($method !== "GET") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                if ($this->getAccessToken()) {
                    $data = json_encode($data);
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        } elseif (!empty($data)) {
            $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getDefaultHeaders() + $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);

        curl_close($curl);

        return json_decode($result);
    }

    private function updateCredentials(object $credentials): bool
    {
        if (!empty($credentials->access_token) && !empty($credentials->refresh_token)) {
            $this->setAccessToken($credentials->access_token);
            $this->setRefreshToken($credentials->refresh_token);

            $this->integration->access_token = $this->getAccessToken();
            $this->integration->refresh_token = $this->getRefreshToken();
            $this->integration->expiration = Carbon::createFromTimestamp($this->getExpiration());
            $this->integration->completed = true;
            $this->integration->save();

            return true;
        }
        return false;
    }

    public function requestAccessToken(string $code): bool
    {
        $data = [
            "grant_type" => "authorization_code",
            "client_id" => $this->getClientId(),
            "client_secret" => $this->getClientSecret(),
            "redirect_uri" => $this->getCallbackUrl(),
            "code" => $code,
        ];

        $result = $this->doRequest("/oauth/token", $data, "POST");

        return $this->updateCredentials($result);
    }

    private function refreshToken(): bool
    {
        $data = [
            "grant_type" => "refresh_token",
            "refresh_token" => $this->getRefreshToken(),
            "client_id" => $this->getClientId(),
            "client_secret" => $this->getClientSecret(),
        ];

        $result = $this->doRequest("/oauth/token", $data, "POST");

        return $this->updateCredentials($result);
    }

    public function getOrder(string $orderId): object
    {
        return $this->doRequest("/api/v2/me/orders/{$orderId}");
    }
}
