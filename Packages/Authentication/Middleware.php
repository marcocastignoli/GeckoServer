<?php
namespace Authentication;

use App;

class Middleware
{
    function __construct(){
        App\Kernel::implementComponents($this, 'Output');
    }
    public function checkToken($request)
    {
        if(@$request['token'] === "qwerty"){
            return true;
        } else {
            $this->JSON->reply("Not authorized");
            return false;
        }
    }
}