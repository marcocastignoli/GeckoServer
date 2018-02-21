<?php

namespace Authentication;

class Routes
{
    public static function default($middleware)
    {   
        return [
            ['GET', 'user/login', 'Authentication', 'login']
        ];
    }
}