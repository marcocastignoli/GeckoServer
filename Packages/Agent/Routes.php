<?php

App\Kernel::addRoute('GET', 'agent/list', 'Agent', 'list' , function($request){
    return $request->Authentication->checkToken(@$request->get('token'));
});
App\Kernel::addRoute('POST', 'agent/create', 'Agent', 'create');