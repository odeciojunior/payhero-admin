<?php

use App\Facades\Whitelabel;

if (!function_exists('whitelabel')) {
    /**
     * Get whitelabel instance or value
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function whitelabel($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('whitelabel');
        }

        return Whitelabel::get($key, $default);
    }
}

if (!function_exists('whitelabel_color')) {
    /**
     * Get whitelabel color
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    function whitelabel_color($key, $default = '#000000')
    {
        return Whitelabel::getColor($key, $default);
    }
}

if (!function_exists('whitelabel_logo')) {
    /**
     * Get whitelabel logo
     *
     * @param string $type
     * @return string
     */
    function whitelabel_logo($type = 'main')
    {
        return Whitelabel::getLogo($type);
    }
}

if (!function_exists('whitelabel_font')) {
    /**
     * Get whitelabel font
     *
     * @param string $type
     * @return array
     */
    function whitelabel_font($type = 'primary')
    {
        return Whitelabel::getFont($type);
    }
}

if (!function_exists('whitelabel_app_name')) {
    /**
     * Get whitelabel app name
     *
     * @return string
     */
    function whitelabel_app_name()
    {
        return Whitelabel::getAppName();
    }
}

if (!function_exists('whitelabel_footer_text')) {
    /**
     * Get whitelabel footer text
     *
     * @return string
     */
    function whitelabel_footer_text()
    {
        return Whitelabel::getFooterText();
    }
}

if (!function_exists('whitelabel_favicon')) {
    /**
     * Get whitelabel favicon
     *
     * @return string
     */
    function whitelabel_favicon()
    {
        return Whitelabel::getFavicon();
    }
}

if (!function_exists('whitelabel_client')) {
    /**
     * Get current whitelabel client
     *
     * @return string
     */
    function whitelabel_client()
    {
        return Whitelabel::getCurrentClient();
    }
}

if (!function_exists('is_whitelabel_client')) {
    /**
     * Check if current client matches given client
     *
     * @param string $client
     * @return bool
     */
    function is_whitelabel_client($client)
    {
        return Whitelabel::getCurrentClient() === $client;
    }
}

if (!function_exists('whitelabel_validate_config')) {
    /**
     * Validate current client configuration
     *
     * @param string|null $client
     * @return array
     */
    function whitelabel_validate_config($client = null)
    {
        return Whitelabel::validateClientConfig($client);
    }
}

if (!function_exists('whitelabel_available_clients')) {
    /**
     * Get list of available clients
     *
     * @return array
     */
    function whitelabel_available_clients()
    {
        return Whitelabel::getAvailableClients();
    }
}