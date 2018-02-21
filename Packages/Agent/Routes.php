<?php

namespace Agent;

class Routes
{
    public static function default($middleware)
    {   
        $middleware = $middleware ? $middleware : 'Authentication';
        return [
            ['GET', 'agent/list', 'Agent', 'list', $middleware],
            ['POST', 'agent/create', 'Agent', 'create', $middleware]
        ];
    }
}