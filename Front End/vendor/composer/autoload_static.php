<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5a97b2e9c4d266b9dab43a2c68466ed3
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'App\\Config' => __DIR__ . '/../..' . '/app/Config.php',
        'App\\SQLiteConnection' => __DIR__ . '/../..' . '/app/SQLiteConnection.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5a97b2e9c4d266b9dab43a2c68466ed3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5a97b2e9c4d266b9dab43a2c68466ed3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5a97b2e9c4d266b9dab43a2c68466ed3::$classMap;

        }, null, ClassLoader::class);
    }
}
