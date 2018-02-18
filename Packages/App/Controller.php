<?php

namespace App;

abstract class Controller
{
    protected $model;
    function __construct($model)
    {
        Kernel::implementComponents($this, 'Output');
        $this->model = $model;
    }
}