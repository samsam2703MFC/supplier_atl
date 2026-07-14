<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addRoute('GET', '/orders', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'index',
    ]);

    $r->addRoute('GET', '/ajax/orders/{orderId:\d+}', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'show',
    ]);

    $r->addRoute('GET', '/orders/{orderId:\d+}', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'details',
    ]);

    $r->addRoute('POST', '/ajax/orders/{orderId:\d+}/accept', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'accept',
    ]);

    $r->addRoute('POST', '/ajax/orders/{orderId:\d+}/reject', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'reject',
    ]);

    $r->addRoute('POST', '/ajax/orders/{orderId:\d+}/cancel', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'cancel',
    ]);

    $r->addRoute('PUT', '/ajax/orders/{orderId:\d+}/final-items', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'updateFinalItems',
    ]);

    $r->addRoute('PUT', '/ajax/orders/{orderId:\d+}/transport', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'updateTransport',
    ]);

    $r->addRoute('POST', '/ajax/orders/{orderId:\d+}/finalization-check', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'finalizationCheck',
    ]);

    $r->addRoute('POST', '/ajax/orders/{orderId:\d+}/finalize', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'finalize',
    ]);

    $r->addRoute('GET', '/ajax/orders/carriers', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'carriers',
    ]);

    $r->addRoute('POST', '/ajax/orders/carriers', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'createCarrier',
    ]);

    $r->addRoute('PUT', '/ajax/orders/carriers/{carrierId:\d+}', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'updateCarrier',
    ]);

    $r->addRoute('POST', '/ajax/orders/carriers/{carrierId:\d+}/deactivate', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'deactivateCarrier',
    ]);

    $r->addRoute('GET', '/ajax/orders/{orderId:\d+}/documents', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'documents',
    ]);

    $r->addRoute('GET', '/ajax/orders/{orderId:\d+}/documents/cmr/status', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'cmrStatus',
    ]);

    $r->addRoute('POST', '/ajax/orders/{orderId:\d+}/documents/differences', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'generateDifferencesDocument',
    ]);

    $r->addRoute('POST', '/ajax/orders/{orderId:\d+}/documents/release', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'generateReleaseDocument',
    ]);

    $r->addRoute('POST', '/ajax/orders/{orderId:\d+}/documents/cmr', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'generateCmrDocument',
    ]);

    $r->addRoute('GET', '/orders/{orderId:\d+}/documents/{documentId:\d+}/download', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'downloadOrderDocument',
    ]);

    $r->addRoute('GET', '/orders/{orderId:\d+}/document', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'downloadDocument',
    ]);

    $r->addRoute('POST', '/orders/documents/download', [
        'controller' => \App\Supplier\app\Http\Controllers\Orders\OrdersController::class,
        'method'     => 'downloadSelectedDocuments',
    ]);
};
