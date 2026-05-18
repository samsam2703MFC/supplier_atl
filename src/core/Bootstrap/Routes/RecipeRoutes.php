<?php

use App\Supplier\app\Http\Controllers\Recipe\RecipeController;
use App\Supplier\app\Http\Controllers\Recipe\RecipeStepController;
use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    // Widok modułu "Receptury"
    $r->addRoute('GET', '/recipes', [
        'controller' => RecipeController::class,
        'method'     => 'index',
    ]);

    // Dedykowana strona kalkulatora receptury
    $r->addRoute('GET', '/recipes/{id:\d+}', [
        'controller' => RecipeController::class,
        'method'     => 'show',
    ]);

    // Kroki przygotowania receptury — widok
    $r->addRoute('GET', '/recipes/{recipeId:\d+}/steps', [
        'controller' => RecipeStepController::class,
        'method'     => 'index',
    ]);

    // -------------------------------------------------------------------
    // Recipes AJAX
    // -------------------------------------------------------------------

    $r->addRoute('GET', '/ajax/recipes', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxGetAll',
    ]);

    $r->addRoute('GET', '/ajax/recipes/{id:\d+}', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxGetOne',
    ]);

    $r->addRoute('POST', '/ajax/recipes', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxInsert',
    ]);

    $r->addRoute('PATCH', '/ajax/recipes/{id:\d+}', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxUpdate',
    ]);

    $r->addRoute('DELETE', '/ajax/recipes/{id:\d+}', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxDelete',
    ]);

    $r->addRoute('PATCH', '/ajax/recipes/{id:\d+}/archive', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxArchive',
    ]);

    $r->addRoute('PATCH', '/ajax/recipes/{id:\d+}/restore', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxRestore',
    ]);

    $r->addRoute('POST', '/ajax/recipes/{id:\d+}/overhead/recalculate', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxRecalculateOverhead',
    ]);

    // -------------------------------------------------------------------
    // Ingredients AJAX
    // -------------------------------------------------------------------

    $r->addRoute('GET', '/ajax/recipes/{recipeId:\d+}/ingredients', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxGetIngredients',
    ]);

    $r->addRoute('GET', '/ajax/recipes/{recipeId:\d+}/summary', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxGetSummary',
    ]);

    $r->addRoute('POST', '/ajax/recipes/{recipeId:\d+}/ingredients', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxInsertIngredient',
    ]);

    $r->addRoute('PATCH', '/ajax/recipes/{recipeId:\d+}/ingredients/{id:\d+}', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxUpdateIngredient',
    ]);

    $r->addRoute('DELETE', '/ajax/recipes/{recipeId:\d+}/ingredients/{id:\d+}', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxDeleteIngredient',
    ]);

    // Raw materials dropdown
    $r->addRoute('GET', '/ajax/recipe-raw-materials', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxGetRawMaterials',
    ]);

    // Labor defaults
    $r->addRoute('GET', '/ajax/labor-defaults', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxGetLaborDefaults',
    ]);

    $r->addRoute('PATCH', '/ajax/labor-defaults', [
        'controller' => RecipeController::class,
        'method'     => 'ajaxUpdateLaborDefaults',
    ]);

    // -------------------------------------------------------------------
    // Steps AJAX
    // -------------------------------------------------------------------

    $r->addRoute('GET', '/ajax/recipes/{recipeId:\d+}/steps', [
        'controller' => RecipeStepController::class,
        'method'     => 'ajaxGetAll',
    ]);

    $r->addRoute('POST', '/ajax/recipes/{recipeId:\d+}/steps', [
        'controller' => RecipeStepController::class,
        'method'     => 'ajaxInsert',
    ]);

    $r->addRoute('PATCH', '/ajax/recipes/{recipeId:\d+}/steps/reorder', [
        'controller' => RecipeStepController::class,
        'method'     => 'ajaxReorder',
    ]);

    $r->addRoute('PATCH', '/ajax/recipes/{recipeId:\d+}/steps/{id:\d+}', [
        'controller' => RecipeStepController::class,
        'method'     => 'ajaxUpdate',
    ]);

    $r->addRoute('DELETE', '/ajax/recipes/{recipeId:\d+}/steps/{id:\d+}', [
        'controller' => RecipeStepController::class,
        'method'     => 'ajaxDelete',
    ]);

    $r->addRoute('POST', '/ajax/recipes/{recipeId:\d+}/steps/{stepId:\d+}/photos/{slot:\d+}', [
        'controller' => RecipeStepController::class,
        'method'     => 'ajaxUploadPhoto',
    ]);

    $r->addRoute('DELETE', '/ajax/recipes/{recipeId:\d+}/steps/{stepId:\d+}/photos/{slot:\d+}', [
        'controller' => RecipeStepController::class,
        'method'     => 'ajaxDeletePhoto',
    ]);
};
