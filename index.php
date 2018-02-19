<?php
include 'Gecko.php';

App\Kernel::initPackages([
    'Authentication',
    'Agent'
]);

$kernel = new App\Kernel();

$kernel->addRoute('GET', 'agent/list', 'Agent', 'list' , function($request){
    return $request->Authentication->checkToken(@$request->get('token'));
});
$kernel->addRoute('POST', 'agent/create', 'Agent', 'create');
$kernel->addRoute('GET', 'user/login', 'Authentication', 'login');

$kernel->serve();