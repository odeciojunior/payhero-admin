<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getCurrentClient()
 * @method static array getCurrentClientConfig()
 * @method static mixed get(string $key, $default = null)
 * @method static string getColor(string $key, string $default = '#000000')
 * @method static string getLogo(string $type = 'main')
 * @method static array getFont(string $type = 'primary')
 * @method static mixed getTypography(string $key)
 * @method static string getAppName()
 * @method static string getFooterText()
 * @method static string getFavicon()
 * @method static string generateStyles()
 * @method static void clearCache()
 * @method static bool setClient(string $client)
 * 
 * @see \App\Services\WhitelabelService
 */
class Whitelabel extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'whitelabel';
    }
}