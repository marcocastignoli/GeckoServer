<?php

App\Kernel::addRoute('GET', 'package/install', 'VirtualBoot', 'install');
App\Kernel::addRoute('GET', 'package/uninstall', 'VirtualBoot', 'uninstall');
App\Kernel::addRoute('GET', 'package/activate', 'VirtualBoot', 'activate');
App\Kernel::addRoute('GET', 'package/deactivate', 'VirtualBoot', 'deactivate');