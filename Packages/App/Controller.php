<?php

namespace App;

abstract class Controller
{
    protected $model;
    protected $response;
    function __construct($model)
    {
        $this->response = Kernel::getResponse();
        $this->model = $model;
    }
    function reply($message, $code = Response::CODE_SUCCESS){
        $this->response->reply($message, $code);
    }
}