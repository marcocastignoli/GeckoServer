<?php

namespace App;

abstract class Component
{
    public static $types;
    static $instance;

    function __construct()
    {

    }
    static function getInstance()
    {
        if (!isset(self::$instance)) {
            $called_class = get_called_class();
            self::$instance = new $called_class();
        }
        return self::$instance;
    }
    static function getTypes()
    {
        return self::$types;
    }
}