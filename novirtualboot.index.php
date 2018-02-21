<?php
include 'Gecko.php';

App\Kernel::initPackages([
    'Authentication',
    'Agent'
]);

$kernel = new App\Kernel();
App\Kernel::loadPackageRoutes('Authentication');
App\Kernel::loadPackageRoutes('Agent');
$kernel->serve();