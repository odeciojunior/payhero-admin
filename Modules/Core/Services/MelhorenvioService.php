<?php

namespace Modules\Core\Services;

use Carbon\Carbon;
use Modules\Core\Entities\MelhorenvioIntegration;

class MelhorenvioService
{

    private const BASE_URL = "https://www.melhorenvio.com.br";

    private const BASE_URL_SANDBOX = "https://sandbox.melhorenvio.com.br";

    // TODO: ajustar para usar somente os escopos necessários
    private const SCOPES = [
        'cart-read', // Visualização dos itens do carrinho
        'cart-write', // Cadastro e edição dos itens do carrinhocompanies-read (Visualização das informações de empresas)
        'companies-write', // Cadastro e edição das informações de empresascoupons-read (Visualização dos cupons cadastrados)
        'coupons-write', // Cadastro de novos cuponsnotifications-read (Visualização das notificações)
        'orders-read', // Visualização das etiquetasproducts-read (Visualização de produtos)
        'products-write', // Cadastro e edição de produtospurchases-read (Visualização das compras)
        'shipping-calculate', // Cotação de fretesshipping-cancel (Cancelamento de etiquetas)
        'shipping-checkout', // Checkout para compra de fretes, utiliza saldo da carteirashipping-companies (Consulta de transaportadoras)
        'shipping-generate', // Geração de novas etiquetasshipping-preview (Pré-visualização de etiquetas)
        'shipping-print', // Impressão de etiquetasshipping-share (Compartilhamento de etiquetas)
        'shipping-tracking', // Rastreio de fretesecommerce-shipping (Cotação e compra de fretes para sua loja)
        'transactions-read', // Visualização das transações da carteirausers-read (Visualização das informações pessoais)
        'users-write', // (Edição das informações pessoais)
    ];

    private string $baseUrl;

    private string $clientId;

    private string $clientSecret;

    private ?string $accessToken;

    private ?string $refreshToken;

    private int $expiration;

    private string $callbackUrl;

    private array $defaultHeaders;

    private ?MelhorenvioIntegration $integration;

    public function __construct(MelhorenvioIntegration $integration = null)
    {
        if ($integration) {
            $this->setIntegration($integration);
            $this->setClientId($integration->client_id);
            $this->setClientSecret($integration->client_secret);
            $this->setAccessToken($integration->access_token);
            $this->setRefreshToken($integration->refresh_token);
            $this->setExpiration();
        }
        $this->setBaseUrl();
        $this->setDefaultHeaders();
        $this->setCallbackUrl();
    }

    public function getIntegration(): MelhorenvioIntegration
    {
        return $this->integration;
    }

    private function setIntegration(MelhorenvioIntegration $integration): void
    {
        $this->integration = $integration;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    private function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    private function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    private function setAccessToken(string $accessToken = null): void
    {
        $this->accessToken = $accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    private function setRefreshToken(string $refreshToken = null): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getExpiration(): int
    {
        return $this->expiration;
    }

    private function setExpiration(): void
    {
        if ($this->accessToken) {
            $payloadBase64 = explode('.', $this->accessToken)[1];
            $payloadJson = base64_decode($payloadBase64);
            $payload = json_decode($payloadJson);
            $this->expiration = $payload->exp;
        } else {
            $this->expiration = 0;
        }
    }

    private function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    private function setBaseUrl(): void
    {
        $this->baseUrl = env('MELHORENVIO_SANDBOX', false) === true
            ? self::BASE_URL_SANDBOX
            : self::BASE_URL;
    }

    private function getDefaultHeaders(): array
    {
        return $this->defaultHeaders;
    }

    private function setDefaultHeaders(): void
    {
        $this->defaultHeaders = [
            'User-Agent: Cloudfox Sirius (help@cloudfox.net)',
            'Accept: application/json',
        ];

        if ($this->getExpiration() > 0 && $this->getExpiration() < time()) {
            $this->refreshToken();
        }

        $accessToken = $this->getAccessToken();
        if (!empty($accessToken)) {
            $this->defaultHeaders[] = 'Content-Type: application/json';
            $this->defaultHeaders[] = 'Authorization: Bearer ' . $accessToken;
        }
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    private function setCallbackUrl(): void
    {
        $this->callbackUrl = route('melhorenvio.finish');
    }

    public function getScopes(): string
    {
        return implode(' ', self::SCOPES);
    }

    public function getAuthorizationUrl()
    {
        $data = [
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getCallbackUrl(),
            'response_type' => 'code',
            'scope' => $this->getScopes()
        ];

        $integration = $this->getIntegration();
        if ($integration) {
            $data['state'] = hashids_encode($integration->id);
        }

        return $this->getBaseUrl() . '/oauth/authorize?' . http_build_query($data);
    }

    private function doRequest(string $uri = '/', array $data = null, string $method = 'GET', array $headers = [])
    {
        $curl = curl_init();

        $url = $this->getBaseUrl() . $uri;

        $method = strtoupper($method);

        if ($method !== 'GET') {
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
            $this->setExpiration();

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
            'grant_type' => 'authorization_code',
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
            'redirect_uri' => $this->getCallbackUrl(),
            'code' => $code
        ];

        $result = $this->doRequest('/oauth/token', $data, 'POST');

        return $this->updateCredentials($result);
    }

    private function refreshToken(): bool
    {
        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->getRefreshToken(),
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
        ];

        $result = $this->doRequest('/oauth/token', $data, 'POST');

        return $this->updateCredentials($result);
    }
}
