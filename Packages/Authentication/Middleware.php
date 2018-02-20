<?php

namespace Authentication;

class Middleware
{
    public static function before(){
        return function($request){
            return $request->Authentication->checkToken(@$request->get('token'));
        };
    }
}