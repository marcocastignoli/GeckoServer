<?php
include 'Gecko.php';

App\Kernel::initPackages([
    'Authentication',
    'Agent'
]);

$kernel = new App\Kernel();

$kernel->addRoute('GET', 'agent/list', 'Agent', 'list' , function($request){
    if(@$token = $request->get('token')){
        return $request->Authentication->checkToken($token);
    } else {
        echo "Need token";
        return false;
    }
});
$kernel->addRoute('POST', 'agent/create', 'Agent', 'create');

$kernel->serve();