<?php

App\Kernel::addRoute('GET', 'agent/list', 'Agent', 'list', 'Authentication');
App\Kernel::addRoute('POST', 'agent/create', 'Agent', 'create');