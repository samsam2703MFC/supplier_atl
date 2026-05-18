<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addRoute('GET', '/analytics', [
        'controller' => \App\Supplier\app\Http\Controllers\Analytics\AnalyticsController::class,
        'method'     => 'index',
    ]);

    $r->addRoute('GET', '/analytics/raw-material-sales-all-shops', [
        'controller' => \App\Supplier\app\Http\Controllers\Analytics\AnalyticsController::class,
        'method'     => 'rawMaterialSalesAllShops',
    ]);
    $r->addRoute('GET', '/analytics/raw-material-sales', [
        'controller' => \App\Supplier\app\Http\Controllers\Analytics\AnalyticsController::class,
        'method'     => 'rawMaterialSales',
    ]);
};
