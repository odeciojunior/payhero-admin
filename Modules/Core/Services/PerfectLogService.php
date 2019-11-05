<?php


namespace Modules\Core\Services;


class PerfectLogService
{
    private const API_URL = 'http://log.devppay.com.br';

    private const API_SYSTEM_TOKEN = '2521e73164e82b63053bad20bcef8ce5cd67bce2896e3744df6ccaf8f574f7eb';

    private const API_USER_TOKEN = 'e9193d463e8c452427643763862e0ed2';


    /**
     * @param $trackingId
     * @param $trackingNumber
     * @return mixed
     */
    public function track($trackingId, $trackingNumber)
    {
        $data = [
            'external_reference' => $trackingId,
            'response_webhook_url' => 'http://dev.cloudfox.com.br/postback/perfectlog',
            'tracking' => $trackingNumber,
            'token_user' => self::API_USER_TOKEN,
            'system' => self::API_SYSTEM_TOKEN,
        ];

        $result = $this->call('/api/tracking', $data, 'POST');

        return json_decode($result);
    }


    /**
     * @param string $uri
     * @param null $data
     * @param string $method
     * @return bool|string
     */
    private function call($uri = '/', $data = null, $method = 'GET')
    {

        $url = self::API_URL . $uri;

        $curl = curl_init();

        $method = strtoupper($method);

        switch ($method) {
            case 'GET':
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

}
