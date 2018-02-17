<?php
namespace Agent;

use App;

App\Kernel::includePackageFile("App", App\Kernel::PACKAGE_CONTROLLER);
App\Kernel::includePackageFile("Agent", App\Kernel::PACKAGE_MODEL);

class Controller extends App\Controller
{
    function __construct()
    {
        parent::__construct(new Model());
    }
    function list()
    {
        $agents = $this->model->get();
        $this->Response->reply($agents->getProperties());
    }
    function create($request)
    {
        if (isset($request['name'])) {
            $agents = $this->model->create($request['name']);
            $this->Response->reply($agents);
        } else {
            $this->Response->reply('Parametro name mancante', App\Response::CODE_MISSING_PARAMETER);
        }
    }
}