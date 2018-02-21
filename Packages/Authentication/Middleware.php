<?php

namespace Authentication;

class Middleware
{
    public static function before(){
        return function($request){
            return $request->Request->checkToken(@$request->get('token'));
        };
    }
}