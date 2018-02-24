<?php

namespace App;

class Request
{
    protected static $headers;

    function __construct($request)
    {
        self::$headers = getallheaders();
        Kernel::implementComponents($this, 'Request');
    }

    public function header($header)
    {
        if(array_key_exists($header, self::$headers)){
            return self::$headers[$header];
        }
        return false;
    }

    public function get($parameter)
    {
        return @$_REQUEST[$parameter];
    }
}