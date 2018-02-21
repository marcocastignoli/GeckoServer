<?php
include 'Gecko.php';
App\Kernel::initPackages([
    'Authentication',
    'VirtualBoot'
]);
$kernel = new App\Kernel();
$kernel->VirtualBoot->boot();
App\Kernel::loadPackageRoutes('Authentication');
App\Kernel::loadPackageRoutes('VirtualBoot', 'Authentication');
$kernel->serve();