<?php

namespace Modules\Core\Services\Nuvemshop;

use Exception;

class NuvemshopAPI
{
    private $base_url;

    private $store_id;

    private $access_token;

    public function __construct($store_id, $access_token)
    {
        $this->base_url = "https://api.nuvemshop.com.br/v1/$store_id";
        $this->store_id = $store_id;
        $this->access_token = $access_token;
    }

    public static function authenticate($token)
    {
        $url = "https://www.tiendanube.com/apps/authorize/token";

        $headers = ["Content-Type: application/json"];

        $data = [
            "client_id" => env("NUVEMSHOP_CLIENT_ID"),
            "client_secret" => env("NUVEMSHOP_CLIENT_SECRET"),
            "grant_type" => "authorization_code",
            "code" => $token,
        ];

        $curl = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
        ];

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        return json_decode($response, true);
    }

    private function get($endpoint)
    {
        return $this->request("GET", $endpoint);
    }

    private function post($endpoint, $data = null)
    {
        return $this->request("POST", $endpoint, $data);
    }

    private function put($endpoint, $data = null)
    {
        return $this->request("PUT", $endpoint, $data);
    }

    private function delete($endpoint)
    {
        return $this->request("DELETE", $endpoint);
    }

    private function request($method, $endpoint, $data = null)
    {
        $url = $this->base_url . $endpoint;

        $headers = [
            "Authentication: bearer {$this->access_token}",
            "Accept: application/json",
            "User-Agent: Azcend API Client",
        ];

        if ($method === "POST" || $method === "PUT") {
            $headers[] = "Content-Type: application/json";
        }

        $curl = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if ($data) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            throw new Exception("Erro na requisição: $error");
        }

        return json_decode($response, true);
    }

    public function createOrder($order_data)
    {
        return $this->post("/orders", $order_data);
    }

    public function cancelOrder($order_id, $cancel_data = [])
    {
        $endpoint = "/orders/" . $order_id . "/cancel";
        return $this->post($endpoint, $cancel_data);
    }

    public function closeOrder($order_id)
    {
        $endpoint = "/orders/" . $order_id . "/close";
        return $this->post($endpoint);
    }

    public function findOrder($order_id, $params = [])
    {
        $endpoint = "/orders/" . $order_id;
        if (!empty($params)) {
            $endpoint .= "?" . http_build_query($params);
        }
        return $this->get($endpoint);
    }

    public function updateOrder($order_id, $order_data)
    {
        $endpoint = "/orders/" . $order_id;
        return $this->put($endpoint, $order_data);
    }

    public function deleteOrder($order_id)
    {
        $endpoint = "/orders/" . $order_id;
        return $this->delete($endpoint);
    }

    public function findAllFulfillments($params = [])
    {
        $endpoint = "/orders";
        if (!empty($params)) {
            $endpoint .= "?" . http_build_query($params);
        }
        $orders = $this->get($endpoint);
        return array_filter($orders, function ($order) {
            return isset($order["fulfillment_status"]) && $order["fulfillment_status"] === "fulfilled";
        });
    }

    public function cancelFulfillment($order_id, $cancel_data = [])
    {
        $endpoint = "/orders/" . $order_id . "/cancel_fulfillment";
        return $this->post($endpoint, $cancel_data);
    }

    public function findProductImage($product_id, $image_id, $params = [])
    {
        $endpoint = "/products/{$product_id}/images/{$image_id}";
        if (!empty($params)) {
            $endpoint .= "?" . http_build_query($params);
        }
        return $this->get($endpoint);
    }

    public function findAllProducts($params = [])
    {
        $endpoint = "/products";
        if (!empty($params)) {
            $endpoint .= "?" . http_build_query($params);
        }
        return $this->get($endpoint);
    }

    public function findProduct($product_id, $params = [])
    {
        $endpoint = "/products/" . $product_id;
        if (!empty($params)) {
            $endpoint .= "?" . http_build_query($params);
        }
        return $this->get($endpoint);
    }

    public function findProductVariant($product_id, $variant_id, $params = [])
    {
        $endpoint = "/products/{$product_id}/variants/{$variant_id}";
        if (!empty($params)) {
            $endpoint .= "?" . http_build_query($params);
        }
        return $this->get($endpoint);
    }

    public function createWebhook($webhook_data)
    {
        return $this->post("/webhooks", $webhook_data);
    }

    public function findAllWebhooks($params = [])
    {
        $endpoint = "/webhooks";
        if (!empty($params)) {
            $endpoint .= "?" . http_build_query($params);
        }
        return $this->get($endpoint);
    }

    public function findWebhook($webhook_id, $params = [])
    {
        $endpoint = "/webhooks/" . $webhook_id;
        if (!empty($params)) {
            $endpoint .= "?" . http_build_query($params);
        }
        return $this->get($endpoint);
    }

    public function deleteWebhook($webhook_id)
    {
        $endpoint = "/webhooks/" . $webhook_id;
        return $this->delete($endpoint);
    }
}
