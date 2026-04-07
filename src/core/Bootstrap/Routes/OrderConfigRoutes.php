<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addRoute('GET', '/me/order-config', [
        'controller' => \App\Supplier\app\Http\Controllers\Me\OrderConfigController::class,
        'method'     => 'index',
    ]);

    $r->addRoute('POST', '/me/order-config', [
        'controller' => \App\Supplier\app\Http\Controllers\Me\OrderConfigController::class,
        'method'     => 'save',
    ]);
};

