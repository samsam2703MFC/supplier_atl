<?php


use FastRoute\RouteCollector;

return function(RouteCollector $r) {

    $r->addRoute('GET', '/me', [
        'controller' => \App\Supplier\app\Http\Controllers\Me\ProfileController::class,
        'method'     => 'index'
    ]);

    $r->addRoute('POST', '/me', [
        'controller' => \App\Supplier\app\Http\Controllers\Me\ProfileController::class,
        'method'     => 'update'
    ]);

    $r->addRoute('POST', '/me/logo', [
        'controller' => \App\Supplier\app\Http\Controllers\Me\ProfileController::class,
        'method'     => 'uploadLogo'
    ]);

};