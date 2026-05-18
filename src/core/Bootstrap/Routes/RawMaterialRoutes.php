<?php

use App\Supplier\app\Http\Controllers\RawMaterial\RawMaterialController;
use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    // Widok modułu "Baza surowców"
    $r->addRoute('GET', '/raw-materials', [
        'controller' => RawMaterialController::class,
        'method'     => 'index',
    ]);

    // AJAX — lista surowców (z filtrami: name, sku, id_category)
    $r->addRoute('GET', '/ajax/raw-materials', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxGetAll',
    ]);

    // AJAX — dodanie surowca
    $r->addRoute('POST', '/ajax/raw-materials', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxInsert',
    ]);

    // AJAX — edycja surowca
    $r->addRoute('PATCH', '/ajax/raw-materials/{id:\d+}', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxUpdate',
    ]);

    // AJAX — kategorie (do filtra i selecta w formularzu)
    $r->addRoute('GET', '/ajax/raw-material-categories', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxGetCategories',
    ]);

    // AJAX — dodanie kategorii
    $r->addRoute('POST', '/ajax/raw-material-categories', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxInsertCategory',
    ]);

    // AJAX — edycja kategorii
    $r->addRoute('PATCH', '/ajax/raw-material-categories/{id:\d+}', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxUpdateCategory',
    ]);

    // AJAX — usunięcie kategorii
    $r->addRoute('DELETE', '/ajax/raw-material-categories/{id:\d+}', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxDeleteCategory',
    ]);

    // AJAX — jednostki miary (do selecta w formularzu)
    $r->addRoute('GET', '/ajax/raw-material-units', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxGetUnits',
    ]);

    // -----------------------------------------------------------------
    // Allergens (iter. 4)
    // -----------------------------------------------------------------

    $r->addRoute('GET', '/ajax/raw-materials/{id:\d+}/allergens', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxGetAllergens',
    ]);

    $r->addRoute('POST', '/ajax/raw-materials/{id:\d+}/allergens', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxAssignAllergen',
    ]);

    $r->addRoute('DELETE', '/ajax/raw-materials/{id:\d+}/allergens/{allergenId:\d+}', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxUnassignAllergen',
    ]);

    // -----------------------------------------------------------------
    // Price history (iter. 5, append-only)
    // -----------------------------------------------------------------

    $r->addRoute('GET', '/ajax/raw-materials/{id:\d+}/price-history', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxGetPriceHistory',
    ]);

    $r->addRoute('POST', '/ajax/raw-materials/{id:\d+}/price-history', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxAddPriceEntry',
    ]);

    // -----------------------------------------------------------------
    // Archive / restore / delete (iter. 6)
    // -----------------------------------------------------------------

    $r->addRoute('PATCH', '/ajax/raw-materials/{id:\d+}/archive', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxArchive',
    ]);

    $r->addRoute('PATCH', '/ajax/raw-materials/{id:\d+}/restore', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxRestore',
    ]);

    $r->addRoute('DELETE', '/ajax/raw-materials/{id:\d+}', [
        'controller' => RawMaterialController::class,
        'method'     => 'ajaxDelete',
    ]);
};

