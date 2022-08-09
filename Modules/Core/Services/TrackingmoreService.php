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
    const API_URL = "https://api.trackingmore.com";

    /**
     * @var string
     */
    const API_VERSION = "v2";

    /**
     * @var string
     */
    private $apiKey;

    /**
     * TrackingmoreService constructor.
     */
    public function __construct()
    {
        $this->apiKey = getenv("TRACKINGMORE_KEY") ?? "";
    }

    /**
     * @param $trackingNumber
     * @return mixed
     * @see https://www.trackingmore.com/api-track-list-all-track.html#get
     */
    public function find($trackingNumber)
    {
        $result = $this->doRequest("/trackings/get", ["numbers" => $trackingNumber]);
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

        if (!empty($result)) {
            $result->already_exists = true;
            return $result;
        } else {
            switch (true) {
                case preg_match('/^\d{14}$/', $trackingNumber):
                    $carrierCode = "dpd-brazil"; //jadlog
                    break;
                case preg_match('/^[A-Z]{2}\d{9}BR$/', $trackingNumber):
                    $carrierCode = "brazil-correios";
                    break;
                case preg_match("/^LP00516\d{9}/", $trackingNumber):
                    $carrierCode = "ltexp";
                    break;
                default:
                    $carrierCode = "cainiao";
                    break;
            }

            $data = [
                "tracking_number" => $trackingNumber,
                "carrier_code" => $carrierCode,
            ];

            if ($optionalParams) {
                $data += $optionalParams;
            }

            $result = $this->doRequest("/trackings/post", $data, "POST");
            $metaCode = $result->meta->code ?? 0;

            $result = $this->find($trackingNumber);

            if (!empty($result)) {
                return $result;
            } else {
                if ($metaCode == 4032 || $metaCode == 4015) {
                    Log::error("TrackingmoreService - Cannot detect courier - " . $trackingNumber);
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
    public function delete($carrierCode, $trackingNumber)
    {
        $result = $this->doRequest("/trackings/{$carrierCode}/{$trackingNumber}", [], "DELETE");

        return $result->meta->code == 200;
    }

    /**
     * @param $trackingNumber
     * @return mixed
     * @see https://www.trackingmore.com/api-carriers-detect-carrier.html
     */
    private function detectCarrier($trackingNumber)
    {
        $data = ["tracking_number" => $trackingNumber];

        $result = $this->doRequest("/carriers/detect", $data, "POST");

        return $result->data[0]->code ?? null;
    }

    /**
     * @return mixed
     * @see https://www.trackingmore.com/api-carriers-list-all-carriers.html
     */
    public function getAllCarriers()
    {
        return $this->doRequest("/carriers");
    }

    /**
     * @param string $uri
     * @param null $data
     * @param string $method
     * @return object
     */
    private function doRequest($uri = "/", $data = null, $method = "GET")
    {
        $url = self::API_URL . "/" . self::API_VERSION . $uri;

        $curl = curl_init();

        $method = strtoupper($method);

        switch ($method) {
            case "GET":
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Trackingmore-Api-Key: " . $this->apiKey,
            "Content-Type: application/json",
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $retry = true;
        while ($retry) {
            $result = curl_exec($curl);
            $result = json_decode($result);
            if (!empty($result) && !empty($result->meta) && !empty($result->meta->code) && $result->meta->code == 429) {
                sleep(1);
            } else {
                $retry = false;
            }
        }
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

        switch ($status) {
            case "pending":
            case "notfound":
            default:
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum("posted");
                break;
            case "transit":
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum("dispatched");
                break;
            case "pickup":
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum("out_for_delivery");
                break;
            case "delivered":
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum("delivered");
                break;
            case "undelivered":
            case "exception":
            case "expired":
                $statusEnum = $trackingModel->present()->getTrackingStatusEnum("exception");
                break;
        }

        return $statusEnum;
    }
}
