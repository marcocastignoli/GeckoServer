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
        if ($name = $request->get('name')) {
            $agents = $this->Agent->create([
                'name' => $name,
                'created_at' => date('Y-m-d H:i:s', time())
            ]);
            $this->Output->reply($agents);
        } else {
            $this->Output->reply('Parametro name mancante', $this->Output->use()::CODE_MISSING_PARAMETER);
        }
    }
    function setName($request)
    {
        if ( ($newName = $request->get('name')) && ($id = (int)$request->get('id'))) {
            $agents = $this->Agent->updateName($newName, $id);
            $this->Output->reply($agents);
        } else {
            $this->Output->reply('Parametro name mancante', $this->Output->use()::CODE_MISSING_PARAMETER);
        }
    }
}