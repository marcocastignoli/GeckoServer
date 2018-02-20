<?php
global $middleware;

$middleware = $middleware ? $middleware : 'Authentication';

App\Kernel::addRoute('GET', 'agent/list', 'Agent', 'list', $middleware);
App\Kernel::addRoute('POST', 'agent/create', 'Agent', 'create', $middleware);