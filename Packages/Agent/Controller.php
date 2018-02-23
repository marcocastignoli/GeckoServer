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
}