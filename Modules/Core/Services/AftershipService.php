<?php


namespace Modules\Core\Services;


use Illuminate\Support\Facades\Log;
use Modules\Core\Entities\Tracking;

class AftershipService
{
    /**
     * @var string
     */
    const API_URL = 'https://api.aftership.com';

    /**
     * @var string
     */
    const API_VERSION = 'v4';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * AftershipService constructor.
     */
    public function __construct()
    {
        $this->apiKey = getenv('AFTERSHIP_KEY') ?? '';
    }

    /**
     * @param null $filters
     * @return mixed
     * @see https://docs.aftership.com/api/4/trackings/get-trackings
     */
    public function getAllTrackings($filters = null)
    {
        $result = $this->call('/trackings', $filters);

        return json_decode($result);
    }

    /**
     * @param $slug
     * @param $trackingNumber
     * @param $optionalParams
     * @return mixed
     * @see https://docs.aftership.com/api/4/trackings/get-trackings-slug-tracking_number
     */
    public function getTracking($slug, $trackingNumber, $optionalParams = null)
    {
        $result = $this->call('/trackings/' . $slug . '/' . $trackingNumber, $optionalParams);

        return json_decode($result);
    }

    /**
     * @param $id
     * @param $optionalParams
     * @return mixed
     * @see https://docs.aftership.com/api/4/trackings/get-trackings-slug-tracking_number Pro Tip Badge!
     */
    public function getTrackingById($id, $optionalParams = null)
    {
        $result = $this->call('/trackings/' . $id, $optionalParams);

        return json_decode($result);
    }

    /**
     * @param $trackingNumber
     * @param null $optionalParams
     * @param null $trackingId
     * @return mixed
     * @see https://docs.aftership.com/api/4/trackings/post-trackings
     */
    public function createTracking($trackingNumber, $optionalParams = null, $trackingId = null)
    {
        $logisticRouterService = new LogisticRouterService();

        $tracking = ['tracking_number' => $trackingNumber];

        if ($optionalParams) {
            $tracking += $optionalParams;
        }

        $data = ['tracking' => $tracking];

        /**
         * Lao Post não detecta automaticamente, é preciso inserir o slug
         * @see https://docs.aftership.com/api/4/couriers/get-couriers
         */
        if ($logisticRouterService->getLogistic($trackingNumber) == 'lao-post') {
            $data['tracking']['slug'] = 'lao-post';
        }

        $response = $this->call('/trackings', $data, 'POST');

        $result = json_decode($response);

        $metaCode = $result->meta->code ?? 0;

        if (!empty($result->data->tracking)) {
            return $result->data->tracking;
        } else {
            //{"meta":{"code":4012,"message":"Cannot detect courier. Activate courier at https://secure.aftership.com/settings/courier","type":"BadRequest"},"data":{"tracking":{"tracking_number":"AA987654321BR"}}}
            if ($metaCode == 4012) {
                Log::error('AftershipService - Cannot detect courier - ' . $trackingNumber);
            }
            return null;
        }
    }

    /**
     * @param string $uri
     * @param null $data
     * @param string $method
     * @return bool|string
     */
    private function call($uri = '/', $data = null, $method = 'GET')
    {
        $url = self::API_URL . '/' . self::API_VERSION . $uri;

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
            'aftership-api-key: ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public function parseStatus($status)
    {
        $trackingModel = new Tracking();

        $statusEnum = 0;

        switch ($status) {
            case 'Pending':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('posted');
                break;
            case 'InTransit':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('dispatched');
                break;
            case 'OutForDelivery':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('out_for_delivery');
                break;
            case 'Delivered':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('delivered');
                break;
            case 'Exception':
            case 'AttemptFail':
            case 'Expired':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('exception');
                break;
        }

        return $statusEnum;
    }
}
