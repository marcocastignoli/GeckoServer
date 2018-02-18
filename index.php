<?php
include 'Gecko.php';

App\Kernel::initPackages([
    'Authentication',
    'Agent'
]);

$kernel = new App\Kernel();

$kernel->addRoute('GET', 'agent/list', 'Agent', 'list' , function($request){
    $authentication = new Authentication\Middleware();
    return $authentication->checkToken($request);
});
$kernel->addRoute('POST', 'agent/create', 'Agent', 'create');

$kernel->serve();