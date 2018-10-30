<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite1faa2b74df59ecfe488cd92f22d9bdb
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\composer\\' => 15,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/application',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite1faa2b74df59ecfe488cd92f22d9bdb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite1faa2b74df59ecfe488cd92f22d9bdb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
