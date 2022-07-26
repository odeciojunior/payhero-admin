<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Modules\Core\DataTransferObjects\BigBoostUserData;
use Modules\Core\DataTransferObjects\BureauUserDataInterface;

class BigBoostService
{
    public function getUserData(string $cpf): BureauUserDataInterface
    {
        return new BigBoostUserData($this->requestUserBasicData($cpf));
    }

    private function requestUserBasicData(string $cpf): array
    {
        if (!env('BIGBOOST_TOKEN')) {
            return [];
        }
        try {
            if (!env('ENABLE_BUREAU_DATA_QUERYING', FoxUtils::isProduction())) {
                return [];
            }
            $cache_key = 'bigboost-user-basic-data-' . $cpf;
            $response = Cache::get($cache_key, []);
            if ($response) {
                return $response;
            }
            $response = Http::acceptJson()
                ->timeout(10)
                ->post(
                    'https://bigboost.bigdatacorp.com.br/peoplev2',
                    [
                        'Datasets' => 'basic_data',
                        'q' => "doc{{$cpf}}",
                        'AccessToken' => env('BIGBOOST_TOKEN'),
                    ]
                );
            Cache::put($cache_key, $response->json(), now()->addDays(7));
            return $response->json();
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }
}
