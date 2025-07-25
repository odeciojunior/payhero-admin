<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7acb9563332034adb2e2cf43e325e812
{
    public static $prefixLengthsPsr4 = [
        "M" => [
            "Modules\\Aplicativos\\" => 20,
        ],
    ];

    public static $prefixDirsPsr4 = [
        "Modules\\Aplicativos\\" => [
            0 => __DIR__ . "/../.." . "/",
        ],
    ];

    public static $classMap = [
        "Modules\\Aplicativos\\Database\\Seeders\\AplicativosDatabaseSeeder" =>
            __DIR__ . "/../.." . "/Database/Seeders/AplicativosDatabaseSeeder.php",
        "Modules\\Aplicativos\\Http\\Controllers\\AplicativosController" =>
            __DIR__ . "/../.." . "/Http/Controllers/AplicativosController.php",
        "Modules\\Aplicativos\\Providers\\AplicativosServiceProvider" =>
            __DIR__ . "/../.." . "/Providers/AplicativosServiceProvider.php",
    ];

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(
            function () use ($loader) {
                $loader->prefixLengthsPsr4 = ComposerStaticInit7acb9563332034adb2e2cf43e325e812::$prefixLengthsPsr4;
                $loader->prefixDirsPsr4 = ComposerStaticInit7acb9563332034adb2e2cf43e325e812::$prefixDirsPsr4;
                $loader->classMap = ComposerStaticInit7acb9563332034adb2e2cf43e325e812::$classMap;
            },
            null,
            ClassLoader::class
        );
    }
}
