<?php

namespace VirtualBoot;

class Routes
{
    public static function default($middleware)
    {   
        $middleware = $middleware ? $middleware : 'Authentication';
        return [
            ['GET', 'package/install', 'VirtualBoot', 'install', $middleware],
            ['GET', 'package/uninstall', 'VirtualBoot', $middleware],
            ['GET', 'package/activate', 'VirtualBoot', $middleware],
            ['GET', 'package/deactivate', 'VirtualBoot', $middleware],
        ];
    }
}