<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite53d1fb5603161e974af6269be0805e6
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInite53d1fb5603161e974af6269be0805e6::$classMap;

        }, null, ClassLoader::class);
    }
}