<?php
include 'Gecko.php';

$kernel = new App\Kernel();

$kernel->addRoute('GET', 'agent/list', 'Agent', 'list'/* , function(){
    App\Kernel::getResponse()->reply("Non autorizzato", App\Response::CODE_MISSING_ROUTE);
    return false;
} */);
$kernel->addRoute('POST', 'agent/create', 'Agent', 'create');

$kernel->serve();