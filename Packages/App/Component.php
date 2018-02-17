<?php

namespace App;

abstract class Component
{
    public static $types;

    function __construct()
    {

    }
    static function getInstance()
    {
        $called_class = get_called_class();
        if (!isset($called_class::$instance)) {
            $called_class::$instance = new $called_class();
        }
        return $called_class::$instance;
    }
    static function getTypes()
    {
        return self::$types;
    }
}