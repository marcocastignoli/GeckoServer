<?php
include 'Gecko.php';

$kernel = new App\Kernel();
App\Kernel::initPackages([
    'Agent'
]);

$kernel->addRoute('GET', 'agent/list', 'Agent', 'list');
$kernel->addRoute('POST', 'agent/create', 'Agent', 'create');

$kernel->serve();