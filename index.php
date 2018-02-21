<?php
include 'Gecko.php';
App\Kernel::initPackages([
    'VirtualBoot'
]);
$kernel = new App\Kernel();
$kernel->VirtualBoot->boot();
App\Kernel::loadPackageRoutes('VirtualBoot');
$kernel->serve();