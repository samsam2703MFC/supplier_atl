<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addRoute('GET', '/orders', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'index',
    ]);
};
