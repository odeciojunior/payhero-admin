<?php

if (!function_exists('hashids')) {
    function hashids()
    {
        return \Vinkla\Hashids\Facades\Hashids::getFacadeRoot();
    }
}

if (!function_exists('hashids_decode')) {
    function hashids_decode(string $hash, $connection = null)
    {
        if($connection) {
            return current((array) \Vinkla\Hashids\Facades\Hashids::connection($connection)->decode($hash));
        }
        return current((array) \Vinkla\Hashids\Facades\Hashids::decode($hash));
    }
}

if (!function_exists('hashids_encode')) {
    function hashids_encode($numbers, $connection = null)
    {
        if($connection) {
            return current((array) \Vinkla\Hashids\Facades\Hashids::connection($connection)->encode($numbers));
        }
        return \Vinkla\Hashids\Facades\Hashids::encode($numbers);
    }
}

if (!function_exists('foxutils')) {
    function foxutils()
    {
        return new \Modules\Core\Services\FoxUtils();
    }
}

if (!function_exists('builder2sql')) {
    function builder2sql($query)
    {
        $bindings = array_map(function ($binding) {
            return is_numeric($binding) ? $binding : "'".$binding."'";
        }, $query->getBindings());
        return str_replace_array('?', $bindings, $query->toSql());
    }
}

if (!function_exists('versionsFile')) {
    function versionsFile()
    {
        return md5(412);
    }
}
