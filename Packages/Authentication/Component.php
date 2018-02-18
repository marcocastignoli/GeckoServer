<?php
namespace Authentication;

use App;

class Component
{
    public static $types = ['Request'];

    function __construct(){
        App\Kernel::implementComponents($this, 'Output');
    }
    public function checkToken($token)
    {
        if($token === "qwerty"){
            return true;
        } else {
            $this->JSON->reply("Not authorized");
            return false;
        }
    }
}