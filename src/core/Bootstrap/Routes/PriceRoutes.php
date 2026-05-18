<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    $r->addRoute('GET', '/price-list', [
        'controller' => \App\Supplier\app\Http\Controllers\Price\PriceController::class,
        'method' => 'globalPriceList'
    ]);

    $r->addRoute('POST', '/ajax/products/{productId:\d+}/prices/all-shops', [
        'controller' => \App\Supplier\app\Http\Controllers\Price\PriceController::class,
        'method' => 'ajaxInsertNewPriceAllShops'
    ]);

    $r->addRoute('DELETE', '/ajax/products/{productId:\d+}/prices/scheduled/{validFrom:[0-9-]+}/all-shops', [
        'controller' => \App\Supplier\app\Http\Controllers\Price\PriceController::class,
        'method' => 'ajaxDeleteAllShops'
    ]);

    $r->addRoute('POST', '/ajax/price-list/import/all-shops', [
        'controller' => \App\Supplier\app\Http\Controllers\Price\PriceController::class,
        'method' => 'ajaxImportPriceListAllShops'
    ]);

};
