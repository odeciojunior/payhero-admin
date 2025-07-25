<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit80990cf90db1af442f77c01d0630882e
{
    public static $prefixLengthsPsr4 = [
        "M" => [
            "Modules\\Notificacoes\\" => 21,
        ],
    ];

    public static $prefixDirsPsr4 = [
        "Modules\\Notificacoes\\" => [
            0 => __DIR__ . "/../.." . "/",
        ],
    ];

    public static $classMap = [
        "Modules\\Notificacoes\\Database\\Seeders\\NotificacoesDatabaseSeeder" =>
            __DIR__ . "/../.." . "/Database/Seeders/NotificacoesDatabaseSeeder.php",
        "Modules\\Notificacoes\\Http\\Controllers\\NotificacoesController" =>
            __DIR__ . "/../.." . "/Http/Controllers/NotificacoesController.php",
        "Modules\\Notificacoes\\Notifications\\Teste" => __DIR__ . "/../.." . "/Notifications/Teste.php",
        "Modules\\Notificacoes\\Providers\\NotificacoesServiceProvider" =>
            __DIR__ . "/../.." . "/Providers/NotificacoesServiceProvider.php",
    ];

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(
            function () use ($loader) {
                $loader->prefixLengthsPsr4 = ComposerStaticInit80990cf90db1af442f77c01d0630882e::$prefixLengthsPsr4;
                $loader->prefixDirsPsr4 = ComposerStaticInit80990cf90db1af442f77c01d0630882e::$prefixDirsPsr4;
                $loader->classMap = ComposerStaticInit80990cf90db1af442f77c01d0630882e::$classMap;
            },
            null,
            ClassLoader::class
        );
    }
}
