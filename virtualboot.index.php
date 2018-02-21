<?php
include 'Gecko.php';
App\Kernel::initPackages([
    'Authentication',
    'VirtualBoot'
]);
$kernel = new App\Kernel();
$kernel->Kernel->__use('VirtualBoot')->boot();
App\Kernel::loadPackageRoutes('Authentication');
App\Kernel::loadPackageRoutes('VirtualBoot', 'Authentication');
$kernel->serve();