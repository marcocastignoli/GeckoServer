<?php

namespace Authentication;

use App;

class Controller extends App\Controller 
{
    public function login($request){
        $username = $request->get('username');
        $password = $request->get('password');
        $jwt = $this->Authentication->getToken($username, $password);
        $this->JSON->reply($jwt);
    }
}