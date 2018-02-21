<?php
namespace Agent;

use App;

class Controller extends App\Controller
{
    function list($request)
    {
        $user = $request->Request->user();
        $agents = $this->Agent->get();
        $this->Output->reply($agents);
    }
    function create($request)
    {
        if (isset($request['name'])) {
            $agents = $this->Agent->create($request['name']);
            $this->Output->reply($agents);
        } else {
            $this->Output->reply('Parametro name mancante', $this->Output->use()::CODE_MISSING_PARAMETER);
        }
    }
}