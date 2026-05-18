<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addRoute('GET', '/logistics', [
        'controller' => \App\Supplier\app\Http\Controllers\Logistics\LogisticsController::class,
        'method'     => 'index',
    ]);
};
