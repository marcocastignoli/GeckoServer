<?php

namespace App;

abstract class Controller
{
    protected $model;
    protected $response;
    function __construct($model)
    {
        Kernel::implementComponents($this, 'Output');
        $this->model = $model;
    }
}