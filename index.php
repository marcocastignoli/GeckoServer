<?php
include 'Gecko.php';

/* App\Kernel::initPackages([
    'VirtualBoot'
]); */

App\Kernel::initPackages([
    'Authentication',
    'Agent'
]);

$kernel = new App\Kernel();
/* $kernel->VirtualBoot->boot(); */

App\Kernel::loadPackageRoutes('Authentication');
App\Kernel::loadPackageRoutes('Agent');

$kernel->serve();