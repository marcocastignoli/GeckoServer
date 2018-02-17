<?php
include 'Gecko.php';

$kernel = new App\Kernel();

$kernel->addRoute('GET', 'agent/list', 'Agent', 'list');
$kernel->addRoute('POST', 'agent/create', 'Agent', 'create');

$kernel->serve();