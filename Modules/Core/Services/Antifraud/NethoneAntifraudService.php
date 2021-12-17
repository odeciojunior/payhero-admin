<?php

namespace Modules\Core\Services\Antifraud;

use Carbon\Carbon;
use Exception;
use Modules\Core\Entities\Antifraud;
use Modules\Core\Entities\NethoneAntifraudTransaction;

class NethoneAntifraudService
{
    private array $exceptions;
    private string $url;
    private $antifraudId;
    private $clientId;
    private $clientSecret;
    private $merchantId;

    public function __construct()
    {
        $this->setAntifraudConfigs();
    }

    private function getAntifraud()
    {
        if (foxutils()->isProduction()) {
            return Antifraud::where('api', 'nethone')
                ->where('environment', 'production')
                ->first();
        }

        return Antifraud::where('api', 'nethone')
            ->where('environment', 'sandbox')
            ->first();
    }

    public function setAntifraudConfigs()
    {
        try {
            $antifraudApi = $this->getAntifraud();
            $this->antifraudId = $antifraudApi->id;
            $this->clientId = $antifraudApi->client_id;
            $this->clientSecret = $antifraudApi->client_secret;
            $this->merchantId = $antifraudApi->merchant_id;
            $this->url = foxutils()->isProduction() ? 'https://api-cfx.nethone.io/v1/' : 'https://api-test-1.nethone.io/v1/';
        } catch (Exception $e) {
            report($e);

            $this->exceptions[] = $e->getMessage();
        }
    }

    public function updateTransactionStatus($saleId, $statusSale)
    {
        try {
            $statusFlag = $this->getStatusEnum($statusSale);

            if (empty($statusFlag)) {
                return;
            }

            $nethoneAntifraud = NethoneAntifraudTransaction::where('sale_id', $saleId)->first();

            if (empty($nethoneAntifraud) || empty($nethoneAntifraud->transaction_id)) {
                return;
            }

            $this->updateTransaction($nethoneAntifraud->transaction_id, ['name' => $statusFlag]);
        } catch (Exception $e) {
            report($e);

            return;
        }
    }

    public function updateTransaction(int $transactionId, array $data)
    {
        $this->sendCurl("transactions/{$transactionId}", 'PUT', $this->getDataForUpdateTransaction($data));
    }

    private function getDataForUpdateTransaction($data): array
    {
        return [
            'flags' => [
                [
                    'name' => $data['name'],
                    'timestamp' => Carbon::now(),
                    'value' => true,
                ]
            ]
        ];
    }

    private function getStatusEnum($saleStatus): string
    {
        switch ($saleStatus) {
            case 'canceled':
                return 'authorization_aborted';
            case 'canceled_antifraud':
                return 'fraud_reported';
            case 'partial_refunded':
            case 'refunded':
                return 'refunded';
            case 'charge_back':
                return 'chargebacked';
            case 'approved':
                return 'authorized';
            default:
                return '';
        }
    }

    private function sendCurl($url, $method, $data = null): void
    {
        $curl = curl_init($this->url . $url);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if (!is_null($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->clientId . ':' . $this->clientSecret);
        curl_exec($curl);
        curl_close($curl);
    }
}

