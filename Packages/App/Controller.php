<?php

namespace App;

abstract class Controller
{
    function __construct()
    {
        $reflector = new \ReflectionClass(get_called_class());
        $packageName = $reflector->getNamespaceName();
        Kernel::implementComponents($this, $packageName);
        Kernel::implementComponents($this, 'Output');
    }
}