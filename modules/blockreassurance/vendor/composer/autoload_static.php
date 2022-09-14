<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit96d6bd1dfc6c95b50f90fd21d83459c2
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PrestaShop\\Module\\BlockReassurance\\' => 35,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PrestaShop\\Module\\BlockReassurance\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'ReassuranceActivity' => __DIR__ . '/../..' . '/classes/ReassuranceActivity.php',
        'blockreassurance' => __DIR__ . '/../..' . '/blockreassurance.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit96d6bd1dfc6c95b50f90fd21d83459c2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit96d6bd1dfc6c95b50f90fd21d83459c2::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit96d6bd1dfc6c95b50f90fd21d83459c2::$classMap;

        }, null, ClassLoader::class);
    }
}
