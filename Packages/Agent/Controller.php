<?php
namespace Agent;

use App;

App\Kernel::includePackageFile("App", App\Kernel::PACKAGE_CONTROLLER);

class Controller extends App\Controller
{
    function list()
    {
        $agents = $this->Agent->get();
        $this->JSON->reply($agents);
    }
    function create($request)
    {
        if (isset($request['name'])) {
            $agents = $this->Agent->create($request['name']);
            $this->JSON->reply($agents);
        } else {
            $this->JSON->reply('Parametro name mancante', $this->JSON::CODE_MISSING_PARAMETER);
        }
    }
}