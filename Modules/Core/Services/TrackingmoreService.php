<?php


namespace Modules\Core\Services;

use Illuminate\Support\Facades\Log;
use Laracasts\Presenter\Exceptions\PresenterException;
use Modules\Core\Entities\Tracking;

class TrackingmoreService
{
    /**
     * @var string
     */
    const API_URL = 'https://api.trackingmore.com';

    /**
     * @var string
     */
    const API_VERSION = 'v2';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * TrackingmoreService constructor.
     */
    public function __construct()
    {
        $this->apiKey = getenv('TRACKINGMORE_KEY') ?? '';
    }

    /**
     * @param $trackingNumber
     * @return mixed
     * @see https://www.trackingmore.com/api-track-list-all-track.html#get
     */
    public function find($trackingNumber)
    {
        $result = $this->call('/trackings/get', ['numbers' => $trackingNumber]);
        $result = json_decode($result);
        return current($result->data->items ?? []) ?? null;
    }

    /**
     * @param $trackingNumber
     * @param null $optionalParams
     * @return mixed
     * @see https://www.trackingmore.com/api-track-create-a-tracking-item.html#post
     */
    public function createTracking($trackingNumber, $optionalParams = null)
    {
        $result = $this->find($trackingNumber);

        if(!empty($result)) {
            $result->already_exists = true;
            return $result;
        } else {

            //jadlog
            if (strlen($trackingNumber) == 14 && preg_match('/^\d+$/', $trackingNumber)) {
                $carrierCode = 'dpd-brazil';
            } else {
                $carrierCode = $this->detectCarrier($trackingNumber);

                if ($carrierCode == "china-ems") {
                    $carrierCode = "china-post";
                }
            }

            $data = [
                'tracking_number' => $trackingNumber,
                'carrier_code' => $carrierCode,
            ];

            if ($optionalParams) {
                $data += $optionalParams;
            }

            $response = $this->call('/trackings/post', $data, 'POST');
            $result = json_decode($response);
            $metaCode = $result->meta->code ?? 0;

            $result = $this->find($trackingNumber);

            if (!empty($result)) {
                return $result;
            } else {
                if ($metaCode == 4032 || $metaCode == 4015) {
                    Log::error('TrackingmoreService - Cannot detect courier - '.$trackingNumber);
                }
                return null;
            }
        }
    }

    /**
     * @param $carrierCode
     * @param $trackingNumber
     * @return mixed
     * @see https://www.trackingmore.com/api-track-delete-a-tracking-item.html#single-delete
     */
    public function delete($carrierCode, $trackingNumber){

        $response = $this->call("/trackings/{$carrierCode}/{$trackingNumber}", [], 'DELETE');

        $result = json_decode($response);

        return $result->meta->code == 200;
    }

    /**
     * @param $trackingNumber
     * @return mixed
     * @see https://www.trackingmore.com/api-carriers-detect-carrier.html
     */
    private function detectCarrier($trackingNumber)
    {
        $data = ['tracking_number' => $trackingNumber];

        $response = $this->call('/carriers/detect', $data, 'POST');

        $result = json_decode($response);

        return $result->data[0]->code ?? null;
    }

    /**
     * @return mixed
     * @see https://www.trackingmore.com/api-carriers-list-all-carriers.html
     */
    public function getAllCarriers()
    {
        $response = $this->call('/carriers');

        return json_decode($response);
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
            'Trackingmore-Api-Key: ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * @param $status
     * @return int
     * @throws PresenterException
     */
    public function parseStatus($status)
    {
        $trackingModel = new Tracking();

        $statusEnum = 0;

        switch ($status) {
            case 'pending':
            case 'notfound':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('posted');
                break;
            case 'transit':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('dispatched');
                break;
            case 'pickup':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('out_for_delivery');
                break;
            case 'delivered':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('delivered');
                break;
            case 'undelivered':
            case 'exception':
            case 'expired':
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum('exception');
                break;
        }

        return $statusEnum;
    }
}
