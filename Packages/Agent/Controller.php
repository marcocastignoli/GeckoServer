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
        $this->JSON->reply($agents->getProperties());
    }
    function create($request)
    {
        if (isset($request['name'])) {
            $agents = $this->model->create($request['name']);
            $this->JSON->reply($agents);
        } else {
            $this->JSON->reply('Parametro name mancante', $this->JSON::CODE_MISSING_PARAMETER);
        }
    }
}