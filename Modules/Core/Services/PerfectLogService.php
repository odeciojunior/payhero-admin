<?php


namespace Modules\Core\Services;


use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Tracking;

class PerfectLogService
{
    private const API_URL = 'https://log.perfectpay.com.br';

    private const API_SYSTEM_TOKEN = 'd2cfc007a524529536dfb43f779ba9fa0711023859ad105aedcfa86252d89ec9';

    private const API_USER_TOKEN = '27aa6d41fd15ba3118159146fd7f89f2';


    /**
     * @param $trackingId
     * @param $trackingNumber
     * @return mixed
     */
    public function track($trackingId, $trackingNumber)
    {
        $data = [
            'external_reference' => $trackingId,
            'response_webhook_url' => 'https://app.cloudfox.net/postback/perfectlog',
            'tracking' => $trackingNumber,
            'token_user' => self::API_USER_TOKEN,
            'system' => self::API_SYSTEM_TOKEN,
        ];

        if (getenv('APP_ENV', 'local') == 'production') {
            $result = $this->call('/api/tracking', $data, 'POST');
        } else {
            $result = '';
        }

        return json_decode($result);
    }

    /**
     * @param $reference
     * @return mixed
     */
    public function find($tracking)
    {
        $data = [
            'tracking' => $tracking,
            'token_user' => self::API_USER_TOKEN,
            'system' => self::API_SYSTEM_TOKEN,
        ];

        $result = $this->call('/api/tracking/search', $data);

        $result = json_decode($result);

        if (!empty($result->data)) {
            return end($result->data);
        } else {
            return null;
        }
    }

    /**
     * @param $apiStatus
     * @return mixed
     * @throws PresenterException
     */
    public function parseStatus($apiStatus)
    {
        $trackingModel = new Tracking();

        $status = 1;

        switch ($apiStatus) {
            //case 'pending':
            case 'preparation':
                $status = $trackingModel->present()->getTrackingStatusEnum('posted');
            case 'sent':
            case 'resend':
                $status = $trackingModel->present()->getTrackingStatusEnum('dispatched');
                break;
            case 'delivered':
                $status = $trackingModel->present()->getTrackingStatusEnum('delivered');
                break;
            case 'out_for_delivery':
                $status = $trackingModel->present()->getTrackingStatusEnum('out_for_delivery');
                break;
            case 'canceled':
            case 'erro_fiscal':
            case 'returned':
                $status = $trackingModel->present()->getTrackingStatusEnum('exception');
                break;
            default:
                $status = $trackingModel->present()->getTrackingStatusEnum('posted');
        }
        return $status;
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
                    report(new \Exception("PerfectLogService - url: " . $url));
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
