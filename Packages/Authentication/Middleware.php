<?php

namespace Authentication;

class Middleware
{
    public static function before(){
        return function($request){
            $token = $request->header("Authentication");
            return $request->Request->checkToken($token);
        };
    }
}