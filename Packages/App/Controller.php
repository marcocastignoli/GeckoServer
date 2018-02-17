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
    function reply($message, $code = Response\Component::CODE_SUCCESS){
        $this->response->reply($message, $code);
    }
}