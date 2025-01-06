<?php

if (!function_exists("hashids")) {
    function hashids()
    {
        return \Vinkla\Hashids\Facades\Hashids::getFacadeRoot();
    }
}

if (!function_exists("hashids_decode")) {
    function hashids_decode(string $hash, $connection = null)
    {
        if ($connection) {
            return current((array) \Vinkla\Hashids\Facades\Hashids::connection($connection)->decode($hash));
        }
        return current((array) \Vinkla\Hashids\Facades\Hashids::decode($hash));
    }
}

if (!function_exists("hashids_encode")) {
    function hashids_encode($numbers, $connection = null)
    {
        if ($connection) {
            return current((array) \Vinkla\Hashids\Facades\Hashids::connection($connection)->encode($numbers));
        }
        return \Vinkla\Hashids\Facades\Hashids::encode($numbers);
    }
}

if (!function_exists("foxutils")) {
    function foxutils(): \Modules\Core\Services\FoxUtils
    {
        return new \Modules\Core\Services\FoxUtils();
    }
}

if (!function_exists("builder2sql")) {
    function builder2sql($query)
    {
        $bindings = array_map(function ($binding) {
            return is_numeric($binding) ? $binding : "'" . $binding . "'";
        }, $query->getBindings());
        return str_replace_array("?", $bindings, $query->toSql());
    }
}

if (!function_exists("getRegionByIp")) {
    function getRegionByIp($ip)
    {
        $regionJson = [];

        $query = @unserialize(file_get_contents("http://ip-api.com/php/" . $ip));
        if ($query && $query["status"] == "success") {
            $regionJson["status"] = $query["status"];
            $regionJson["country"] = $query["country"];
            $regionJson["countryCode"] = $query["countryCode"];
            $regionJson["region"] = $query["region"];
            $regionJson["regionName"] = $query["regionName"];
            $regionJson["city"] = $query["city"];
            $regionJson["zip"] = $query["zip"];
            $regionJson["lat"] = $query["lat"];
            $regionJson["lon"] = $query["lon"];
            $regionJson["timezone"] = $query["timezone"];
            $regionJson["isp"] = $query["isp"];
            $regionJson["org"] = $query["org"];
            $regionJson["as"] = $query["as"];
            $regionJson["query"] = $query["query"];
        }

        return $regionJson;
    }
}

if (!function_exists("versionsFile")) {
    function versionsFile()
    {
        return md5(420);
    }
}

if (!function_exists("getRegionByIp")) {
    function getRegionByIp($ip)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://geolocation-db.com/json/" . $ip,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json($err);
        }

        return $response;
    }
}
