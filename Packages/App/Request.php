<?php

namespace App;

class Request
{
    function __construct($request)
    {
        Kernel::implementComponents($this, 'Request');
    }
    public function get($parameter){
        return @$_REQUEST[$parameter];
    }
}