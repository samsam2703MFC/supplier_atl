<?php
namespace App\Supplier\app\Http\Controllers\Recipe;

use App\Supplier\app\Http\Controllers\Controller;
use App\Supplier\app\Services\Catalog\ProductService;
use App\Supplier\app\Services\Me\SupplierService;
use App\Supplier\app\Services\RawMaterial\RawMaterialService;
use App\Supplier\app\Services\Recipe\RecipeIngredientService;
use App\Supplier\app\Services\Recipe\RecipeService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Kontroler UI modułu "Receptury" (supplier portal).
 *
 * Widok listy receptur + AJAX CRUD + składniki + podsumowanie.
 * Wszystkie operacje są scoped do zalogowanego dostawcy w warstwie serwisu.
 */
class RecipeController extends Controller
{
    public function __construct(
        private RecipeService            $recipeService,
        private RecipeIngredientService  $ingredientService,
        private RawMaterialService       $rawMaterialService,
        private ProductService           $productService,
        private SupplierService          $supplierService,
    ) {}

    // -----------------------------------------------------------------------
    //  Page view
    // -----------------------------------------------------------------------

    public function index(): void
    {
        $supplier  = $this->supplierService->getMe();
        $products  = $this->productService->getAll();

        $data = [
            'products' => $products,
            'currency' => $supplier ? $supplier->getCurrency() : 'EUR',
        ];

        $this->view('recipes/index', $data);
    }

    /**
     * Dedykowana strona kalkulatora receptury.
     * Renderuje tiles: Product + Raw Materials (+ kolejne w przyszłości).
     */
    public function show($id): void
    {
        $supplier     = $this->supplierService->getMe();
        $products     = $this->productService->getAll();
        $recipe       = $this->recipeService->getById((int) $id);
        $ingredients  = $this->ingredientService->getAll((int) $id);
        $summary      = $this->ingredientService->getSummary((int) $id);
        $rawMaterials = $this->rawMaterialService->getAll(['is_archived' => 0]);
        $laborDefaults = $this->supplierService->getLaborDefaults();

        $data = [
            'recipe'            => $recipe,
            'products'          => $products,
            'ingredients'       => $ingredients,
            'summary'           => $summary,
            'rawMaterials'      => $rawMaterials,
            'laborDefaults'     => $laborDefaults,
            'currency'          => $supplier ? $supplier->getCurrency() : 'EUR',
            'defaultPortionUnit' => $this->resolveDefaultPortionUnit($recipe),
        ];

        $this->view('recipes/show', $data);
    }

    private function resolveDefaultPortionUnit(array $recipe): string
    {

    // 1. Use the saved portion_unit if already set
        $saved = $recipe['sales_unit']['portion_unit'] ?? null;

        if ($saved) {
            return $saved;
        }

        // 2. Use product_package_unit from the recipe row (joined from supplier_catalog_product)
        $fromJoin = $recipe['product_package_unit'] ?? null;
        if ($fromJoin) {
            return $fromJoin;
        }

        return 'g';
    }

    // -----------------------------------------------------------------------
    //  Recipes AJAX
    // -----------------------------------------------------------------------

    public function ajaxGetAll()
    {
        $filters = [
            'name'        => $_GET['name']       ?? null,
            'product_id'  => $_GET['product_id'] ?? null,
            'is_archived' => $_GET['is_archived'] ?? null,
        ];

        $items = $this->recipeService->getAll($filters);
        return $this->json(['success' => true, 'data' => $items]);
    }

    public function ajaxGetOne($id)
    {
        $resp = $this->recipeService->getById((int) $id);
        return $this->json(['success' => true, 'data' => $resp]);
    }

    public function ajaxInsert()
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->recipeService->insert($data);

        // Automatycznie zapisz domyślne parametry pracy dla nowo utworzonej receptury
        if (($resp['success'] ?? false) && !empty($resp['inserted_id'])) {
            $defaults  = $this->supplierService->getLaborDefaults();
            $laborData = [];
            if (isset($defaults['labor_time_default']) && $defaults['labor_time_default'] !== null) {
                $laborData['labor_time_per_unit_min'] = (float) $defaults['labor_time_default'];
            }
            if (isset($defaults['labor_rate_default']) && $defaults['labor_rate_default'] !== null) {
                $laborData['labor_hourly_rate'] = (float) $defaults['labor_rate_default'];
            }
            if (!empty($laborData)) {
                $this->recipeService->update((int) $resp['inserted_id'], $laborData);
            }
        }

        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxUpdate($id)
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->recipeService->update((int) $id, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxDelete($id)
    {
        $resp = $this->recipeService->delete((int) $id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxArchive($id)
    {
        $resp = $this->recipeService->archive((int) $id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxRestore($id)
    {
        $resp = $this->recipeService->restore((int) $id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxRecalculateOverhead($id)
    {
        $resp = $this->recipeService->recalculateOverhead((int) $id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    // -----------------------------------------------------------------------
    //  Ingredients AJAX
    // -----------------------------------------------------------------------

    public function ajaxGetIngredients($recipeId)
    {
        $items = $this->ingredientService->getAll((int) $recipeId);
        return $this->json(['success' => true, 'data' => $items]);
    }

    public function ajaxGetSummary($recipeId)
    {
        $summary = $this->ingredientService->getSummary((int) $recipeId);
        return $this->json(['success' => true, 'data' => $summary]);
    }

    public function ajaxInsertIngredient($recipeId)
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->ingredientService->insert((int) $recipeId, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxUpdateIngredient($recipeId, $id)
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->ingredientService->update((int) $recipeId, (int) $id, $data);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    public function ajaxDeleteIngredient($recipeId, $id)
    {
        $resp = $this->ingredientService->delete((int) $recipeId, (int) $id);
        return $this->json($resp, $resp['code'] ?? 200);
    }

    // -----------------------------------------------------------------------
    //  Raw materials dropdown (for ingredient add form)
    // -----------------------------------------------------------------------

    public function ajaxGetRawMaterials()
    {
        $items = $this->rawMaterialService->getAll(['is_archived' => 0]);
        return $this->json(['success' => true, 'data' => $items]);
    }

    // -----------------------------------------------------------------------
    //  Labor defaults — GET / PATCH
    // -----------------------------------------------------------------------

    public function ajaxGetLaborDefaults()
    {
        $resp = $this->supplierService->getLaborDefaults();
        return $this->json(['success' => true, 'data' => $resp]);
    }

    public function ajaxUpdateLaborDefaults()
    {
        $request = Request::createFromGlobals();
        $data    = $this->getJson($request);

        $resp = $this->supplierService->updateLaborDefaults($data);
        return $this->json($resp, $resp['code'] ?? 200);
    }
}
